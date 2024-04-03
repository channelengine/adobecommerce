<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
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
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder;
use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

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
     * @param OrderRepositoryInterface $orderRepository
     * @param ServiceInputProcessor $serviceInputProcessor
     * @param ProductRepository $productRepository
     * @param OrdersConfigurationService $orderSettingsService
     * @param ChannelEngineOrderFactory $orderExtensionFactory
     * @param ChannelEngineOrder $orderExtensionResource
     */
    public function __construct(
        OrderRepositoryInterface   $orderRepository,
        ServiceInputProcessor      $serviceInputProcessor,
        ProductRepository          $productRepository,
        OrdersConfigurationService $orderSettingsService,
        ChannelEngineOrderFactory  $orderExtensionFactory,
        ChannelEngineOrder         $orderExtensionResource
    ) {
        $this->orderRepository = $orderRepository;
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->productRepository = $productRepository;
        $this->orderSettingsService = $orderSettingsService;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderExtensionResource = $orderExtensionResource;
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
                'state' => 'new',
                'tax_amount' => $order->getSubTotalVat(),
                'status' => $this->getStatus($order->getStatus()),
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
                        $order->getBillingAddress()->getStreetName() . ' ' .
                        $order->getBillingAddress()->getHouseNumber() . ' ' .
                        $order->getBillingAddress()->getHouseNumberAddition(),
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
                                        $order->getShippingAddress()->getStreetName() . ' ' .
                                        $order->getShippingAddress()->getHouseNumber() . ' ' .
                                        $order->getShippingAddress()->getHouseNumberAddition(),
                                    ],
                                    'telephone' => $order->getPhone(),
                                ],
                                'method' => 'channelengine_carrier',
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

            $items[] = [
                'base_original_price' => $lineItem->getOriginalUnitPriceInclVat() - $lineItem->getOriginalUnitVat(),
                'base_price' => $lineItem->getOriginalUnitPriceInclVat() - $lineItem->getOriginalUnitVat(),
                'base_price_incl_tax' => $lineItem->getUnitPriceInclVat(),
                'base_row_total' => $lineItem->getLineTotalInclVat() - $lineItem->getLineVat(),
                'base_tax_amount' => $lineItem->getLineVat(),
                'tax_amount' => $lineItem->getLineVat(),
                'tax_percent' => $lineItem->getVatRate(),
                'name' => $lineItem->getDescription(),
                'original_price' => $lineItem->getOriginalUnitPriceInclVat() - $lineItem->getOriginalUnitVat(),
                'price' => $lineItem->getOriginalUnitPriceInclVat(),
                'price_incl_tax' => $lineItem->getOriginalUnitPriceInclVat(),
                'product_id' => $product ? $product->getId() : $lineItem->getMerchantProductNo(),
                'product_type' => $product ? $product->getTypeId() : 'simple',
                'qty_ordered' => $lineItem->getQuantity(),
                'row_total' => $lineItem->getLineTotalInclVat() - $lineItem->getLineVat(),
                'row_total_incl_tax' => $lineItem->getLineTotalInclVat(),
                'sku' => $product ? $product->getSku() : $lineItem->getMerchantProductNo(),
            ];
        }

        return $items;
    }

    /**
     * @param string $status
     *
     * @return string
     * @throws InvalidOrderStatusException
     */
    private function getStatus(string $status): string
    {
        switch ($status) {
            case 'NEW':
                return 'processing';
            case 'CLOSED':
            case 'SHIPPED':
                return 'complete';
        }

        throw new InvalidOrderStatusException('Order import is not available for orders with status ' . $status);
    }

    /**
     * @return OrderSyncConfig
     * @throws QueryFilterInvalidParamException
     */
    private function getOrderSettings(): OrderSyncConfig
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
}
