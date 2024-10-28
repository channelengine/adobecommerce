<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\OrderStatusMappings;
use ChannelEngine\ChannelEngineIntegration\Exceptions\InvalidOrderStatusException;
use ChannelEngine\ChannelEngineIntegration\Exceptions\OrderExcludedException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Authorization\DTO\AccountInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Authorization\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\LineItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Order;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Domain\CreateResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\OrdersService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrderFactory;
use ChannelEngine\ChannelEngineIntegration\Model\ProcessReservationItems;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder;
use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\InventorySales\Model\ReturnProcessor\GetSalesChannelForOrder;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;

/**
 * Class OrderService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class OrderService extends OrdersService
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ServiceInputProcessor
     */
    private $serviceInputProcessor;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var OrdersConfigurationService
     */
    private $orderSettingsService;
    /**
     * @var OrderSyncConfig
     */
    private $orderSettings;
    /**
     * @var ChannelEngineOrderFactory
     */
    private $orderExtensionFactory;
    /**
     * @var ChannelEngineOrder
     */
    private $orderExtensionResource;
    /**
     * @var Proxy
     */
    private $authProxy;
    /**
     * @var AccountInfo
     */
    private $accountInfo;
    /**
     * @var array
     */
    private $orderExtensionData = null;
    /**
     * @var AttributeMappings
     */
    private $mappings;
    /**
     * @var AttributeMappingsService
     */
    private $attributeMappingsService;
    /**
     * @var StatusCollectionFactory
     */
    private $statusCollectionFactory;
    /**
     * @var SalesEventInterfaceFactory
     */
    private $salesEventFactory;

    /**
     * @var ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

    /**
     * @var PlaceReservationsForSalesEventInterface
     */
    private $placeReservationsForSalesEvent;

    /**
     * @var SalesEventExtensionFactory
     */
    private $salesEventExtensionFactory;

    /**
     * @var GetSalesChannelForOrder
     */
    private $getSalesChannelForOrder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ServiceInputProcessor $serviceInputProcessor
     * @param ProductRepository $productRepository
     * @param OrdersConfigurationService $orderSettingsService
     * @param ChannelEngineOrderFactory $orderExtensionFactory
     * @param ChannelEngineOrder $orderExtensionResource
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param SalesEventInterfaceFactory $salesEventFactory
     * @param ItemToSellInterfaceFactory $itemsToSellFactory
     * @param PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param SalesEventExtensionFactory $salesEventExtensionFactory
     * @param GetSalesChannelForOrder $getSalesChannelForOrder
     */
    public function __construct(
        OrderRepositoryInterface                $orderRepository,
        ServiceInputProcessor                   $serviceInputProcessor,
        ProductRepository                       $productRepository,
        OrdersConfigurationService              $orderSettingsService,
        ChannelEngineOrderFactory               $orderExtensionFactory,
        ChannelEngineOrder                      $orderExtensionResource,
        StatusCollectionFactory                 $statusCollectionFactory,
        SalesEventInterfaceFactory              $salesEventFactory,
        ItemToSellInterfaceFactory              $itemsToSellFactory,
        PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        SalesEventExtensionFactory              $salesEventExtensionFactory,
        GetSalesChannelForOrder                 $getSalesChannelForOrder
    ) {
        $this->orderRepository = $orderRepository;
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->productRepository = $productRepository;
        $this->orderSettingsService = $orderSettingsService;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderExtensionResource = $orderExtensionResource;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->salesEventFactory = $salesEventFactory;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->salesEventExtensionFactory = $salesEventExtensionFactory;
        $this->getSalesChannelForOrder = $getSalesChannelForOrder;
    }

    /**
     * @inheritDoc
     */
    public function create(Order $order): CreateResponse
    {
        try {
            if (in_array($order->getStatus(), ['CLOSED', 'SHIPPED'], true) &&
                !$this->getOrderSettings()->isEnableOrdersByMarketplaceSync()) {
                return $this->createResponse(
                    true,
                    '',
                    'Orders fulfilled by marketplace are not imported.'
                );
            }
            $existingOrderData = $this->orderExtensionResource->getOrderByChannelOrderNo($order->getChannelOrderNo());

            $magentoOrder = $this->serviceInputProcessor->process(
                OrderRepositoryInterface::class,
                'save',
                $this->map($order, $existingOrderData)
            );
            $createdOrder = $this->orderRepository->save(reset($magentoOrder));

            if (!$existingOrderData) {
                $this->saveOrderExtensionData($order, $createdOrder);
            }

            if ($this->getOrderSettings() && $this->getOrderSettings()->isCreateReservationsEnabled()) {
                $processReservationItems = new ProcessReservationItems(
                    $this->salesEventFactory,
                    $this->itemsToSellFactory,
                    $this->placeReservationsForSalesEvent,
                    $this->salesEventExtensionFactory,
                    $this->getSalesChannelForOrder
                );
                $processReservationItems->execute($createdOrder);
            }

        } catch (Exception $e) {
            return $this->createResponse(false, '', $e->getMessage());
        }

        return $this->createResponse(true, $createdOrder->getEntityId(), 'Successfully created order.');
    }

    /**
     * @param Order $order
     * @param OrderInterface $magentoOrder
     *
     * @return void
     * @throws AlreadyExistsException
     */
    private function saveOrderExtensionData(Order $order, OrderInterface $magentoOrder): void
    {
        $extensionData = $this->orderExtensionFactory->create();
        $extensionData->setOrderId($magentoOrder->getEntityId());
        $extensionData->setChannelName($order->getChannelName());
        $extensionData->setChannelOrderNo($order->getChannelOrderNo());
        $extensionData->setChannelTypeOfFulfillment($order->getTypeOfFulfillment());
        $extensionData->setOrderCancelled(false);

        $this->orderExtensionResource->save($extensionData);
    }

    /**
     * @param Order $order
     *
     * @return array
     * @throws LocalizedException
     */
    private function getOrderExtensionData(Order $order): array
    {
        if ($this->orderExtensionData === null) {
            $this->orderExtensionData =
                $this->orderExtensionResource->getOrderByChannelOrderNo($order->getChannelOrderNo());
        }

        return $this->orderExtensionData;
    }

    /**
     * @param bool $status
     * @param string $shopOrderId
     * @param string $message
     *
     * @return CreateResponse
     */
    private function createResponse(bool $status, string $shopOrderId = '', string $message = ''): CreateResponse
    {
        $response = new CreateResponse();
        $response->setSuccess($status);
        $response->setShopOrderId($shopOrderId);
        $response->setMessage($message);

        return $response;
    }

    /**
     * @param Order $order
     *
     * @return array|array[]
     * @throws QueryFilterInvalidParamException
     * @throws InvalidOrderStatusException
     * @throws OrderExcludedException
     * @throws LocalizedException
     */
    private function map(Order $order, array $ceOrderData): array
    {
        $addressFormat = $this->getOrderSettings()->getAddressFormat();

        $mappedEntity = [
            'entity' => [
                'customer_email' => $order->getEmail(),
                'customer_firstname' => $order->getBillingAddress()->getFirstName(),
                'customer_is_guest' => 1,
                'customer_lastname' => $order->getBillingAddress()->getLastName(),
                'store_id' => ConfigurationManager::getInstance()->getContext(),
                'order_currency_code' => $this->getAccountInfo()->getCurrencyCode(),
                'base_currency_code' => $this->getAccountInfo()->getCurrencyCode(),
                'grand_total' => $order->getTotalInclVat(),
                'base_grand_total' => $order->getTotalInclVat() - $order->getTotalVat(),
                'shipping_amount' => $order->getShippingCostsInclVat(),
                'tax_amount' => $order->getSubTotalVat(),
                'status' => $this->getStatusAndState($order->getStatus())['status'],
                'state' => $this->getStatusAndState($order->getStatus())['state'],
                'total_paid' => $order->getTotalInclVat(),
                'subtotal' => $order->getSubTotalInclVat() - $order->getSubTotalVat(),
                'payment' => [
                    'method' => 'channelengine_payment',
                ],
                'billing_address' => [
                    'email' => $order->getEmail(),
                    'region' => $order->getBillingAddress()->getRegion(),
                    'country_id' => $order->getBillingAddress()->getCountryIso(),
                    'street' => [
                        $this->formatAddress(
                            $order->getBillingAddress()->getStreetName(),
                            $order->getBillingAddress()->getHouseNumber(),
                            $order->getBillingAddress()->getHouseNumberAddition(),
                            $addressFormat
                        ),
                    ],
                    'postcode' => $order->getBillingAddress()->getZipCode(),
                    'city' => $order->getBillingAddress()->getCity(),
                    'telephone' => $order->getPhone(),
                    'firstname' => $order->getBillingAddress()->getFirstName(),
                    'lastname' => $order->getBillingAddress()->getLastName(),
                    'company' => $order->getBillingAddress()->getCompanyName(),
                    'vat_id' => $order->getVatNo(),
                    'vat_is_valid' => 1,
                ],
                'extension_attributes' => [
                    'shipping_assignments' => [
                        [
                            'shipping' => [
                                'address' => [
                                    'address_type' => 'shipping',
                                    'city' => $order->getShippingAddress()->getCity(),
                                    'company' => $order->getShippingAddress()->getCompanyName(),
                                    'country_id' => $order->getShippingAddress()->getCountryIso(),
                                    'email' => $order->getEmail(),
                                    'firstname' => $order->getShippingAddress()->getFirstName(),
                                    'lastname' => $order->getShippingAddress()->getLastName(),
                                    'postcode' => $order->getShippingAddress()->getZipCode(),
                                    'region' => $order->getShippingAddress()->getRegion(),
                                    'street' => [
                                        $this->formatAddress(
                                            $order->getShippingAddress()->getStreetName(),
                                            $order->getShippingAddress()->getHouseNumber(),
                                            $order->getShippingAddress()->getHouseNumberAddition(),
                                            $addressFormat
                                        ),
                                    ],
                                    'telephone' => $order->getPhone(),
                                ],
                                'method' => 'channelengine_carrier_channelengine_carrier',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (isset($ceOrderData[0]['order_id'])) {
            //Set entity ID if order exists
            $mappedEntity['entity']['entity_id'] = $ceOrderData[0]['order_id'];
        } else {
            //Setting order items will be skipped if order exists since it will add new items
            $mappedEntity['entity']['items'] = $this->mapItems($order->getLines());
        }

        return $mappedEntity;
    }

    /**
     * @param LineItem[] $lineItems
     *
     * @return array
     * @throws OrderExcludedException
     * @throws QueryFilterInvalidParamException
     */
    private function mapItems(array $lineItems): array
    {
        $items = [];
        $mappings = $this->getMappings();

        foreach ($lineItems as $lineItem) {
            try {
                $product = ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID) ?
                    $this->productRepository->getById($lineItem->getMerchantProductNo()) :
                    $this->productRepository->get($lineItem->getMerchantProductNo());
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if (!$product &&
                $this->getOrderSettings()->getUnknownLinesHandling() === OrdersConfigurationService::EXCLUDE_FULL) {
                throw new OrderExcludedException('Order contains unknown product.');
            }

            $item = [
                'base_original_price' => $lineItem->getUnitPriceInclVat() - $lineItem->getUnitVat(),
                'base_price' => $lineItem->getUnitPriceInclVat() - $lineItem->getUnitVat(),
                'base_price_incl_tax' => $lineItem->getUnitPriceInclVat(),
                'base_row_total' => $lineItem->getLineTotalInclVat() - $lineItem->getLineVat(),
                'base_tax_amount' => $lineItem->getLineVat(),
                'tax_amount' => $lineItem->getLineVat(),
                'tax_percent' => $lineItem->getVatRate(),
                'name' => $lineItem->getDescription(),
                'original_price' => $lineItem->getUnitPriceInclVat() - $lineItem->getUnitVat(),
                'price' => $lineItem->getUnitPriceInclVat(),
                'price_incl_tax' => $lineItem->getUnitPriceInclVat(),
                'product_type' => $product ? $product->getTypeId() : 'simple',
                'qty_ordered' => $lineItem->getQuantity(),
                'row_total' => $lineItem->getLineTotalInclVat() - $lineItem->getLineVat(),
                'row_total_incl_tax' => $lineItem->getLineTotalInclVat(),
                'sku' => $product ? $product->getSku() : $lineItem->getMerchantProductNo(),
            ];

            if($product) {
                $item['product_id'] = $product->getId();
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param string $status
     *
     * @return array
     * @throws InvalidOrderStatusException
     * @throws QueryFilterInvalidParamException
     */
    private function getStatusAndState(string $status): array
    {
        $orderStatusMappings = $this->getOrderStatusMappingService()->getOrderStatusMappings();
        $availableStatusAndStates = $this->statusCollectionFactory->create()->joinStates()->toArray()['items'];
        switch ($status) {
            case 'NEW':
                $response['status'] = $orderStatusMappings ? $orderStatusMappings->getStatusOfIncomingOrders() :
                    OrderStatusMappings::DEFAULT_INCOMING_ORDER_STATUS;
                break;
            case 'CLOSED':
                $response['status'] = $orderStatusMappings ? $orderStatusMappings->getStatusOfFulfilledOrders() :
                    OrderStatusMappings::DEFAULT_FULFILLED_ORDER_STATUS;
                break;
            case 'SHIPPED':
                $response['status'] = $orderStatusMappings ? $orderStatusMappings->getStatusOfShippedOrders() :
                    OrderStatusMappings::DEFAULT_SHIPPED_ORDER_STATUS;
                break;
            default:
                throw new InvalidOrderStatusException('Order import is not available for orders with status ' . $status);
        }

        $filteredStatuses = array_filter(
            $availableStatusAndStates,
            static function (array $value) use ($response) {
                return $response['status'] === $value['status'];
            }
        );
        if (empty($filteredStatuses)) {
            throw new InvalidOrderStatusException('Order import is not available for orders with status ' . $status);
        }

        $response['state'] = reset($filteredStatuses)['state'];

        return $response;
    }

    /**
     * @return OrderSyncConfig|null
     * @throws QueryFilterInvalidParamException
     */
    private function getOrderSettings(): ?OrderSyncConfig
    {
        if ($this->orderSettings === null) {
            $this->orderSettings = $this->orderSettingsService->getOrderSyncConfig();
        }

        return $this->orderSettings;
    }

    /**
     * @return AttributeMappings|null
     * @throws QueryFilterInvalidParamException
     */
    private function getMappings(): ?AttributeMappings
    {
        if ($this->mappings === null) {
            $this->mappings = $this->getMappingsService()->getAttributeMappings();
        }

        return $this->mappings;
    }

    /**
     * @return AccountInfo
     */
    private function getAccountInfo(): AccountInfo
    {
        if ($this->accountInfo === null) {
            $this->accountInfo = $this->getAuthProxy()->getAccountInfo();
        }

        return $this->accountInfo;
    }

    /**
     * @return Proxy
     */
    private function getAuthProxy(): Proxy
    {
        if ($this->authProxy === null) {
            $this->authProxy = ServiceRegister::getService(Proxy::class);
        }

        return $this->authProxy;
    }

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        if ($this->attributeMappingsService === null) {
            $this->attributeMappingsService = ServiceRegister::getService(AttributeMappingsService::class);
        }

        return $this->attributeMappingsService;
    }

    /**
     * @return OrderStatusMappingService
     */
    private function getOrderStatusMappingService(): OrderStatusMappingService
    {
        return ServiceRegister::getService(OrderStatusMappingService::class);
    }

    /**
     * Formats the address based on the given address format (US or EU).
     *
     * This function takes in the street name, house number, and an optional house number addition
     * (e.g., apartment number or suite) and returns a formatted address string. The formatting
     * depends on the provided address format.
     *
     * - For US format, the house number and its addition are placed before the street name.
     * - For EU (default) format, the street name comes first, followed by the house number
     *   and its addition (if any).
     *
     * @param string $streetName The name of the street.
     * @param string|null $houseNumber The house number, can be null.
     * @param string|null $houseNumberAddition Optional house number addition (e.g., apartment number), can be null.
     * @param string $addressFormat The address format (either 'us' or 'eu').
     *
     * @return string                     Returns the formatted address as a string.
     */
    private function formatAddress(
        string  $streetName,
        ?string $houseNumber,
        ?string $houseNumberAddition,
        string  $addressFormat
    ): string
    {
        $houseNumber = $houseNumber ?? '';
        $houseNumberAddition = $houseNumberAddition ?? '';

        if ($addressFormat === 'us') {
            return trim("$houseNumber $houseNumberAddition $streetName");
        }

        return trim("$streetName $houseNumber $houseNumberAddition");
    }
}