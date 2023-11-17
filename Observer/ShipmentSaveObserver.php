<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ShipmentRejectedException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\Exceptions\FailedToRetrieveOrdersChannelSupportEntityException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\OrdersChannelSupportCache;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts\ShipmentsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\CreateShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Handlers\ShipmentsCreateRequestHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrder;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder\CollectionFactory;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ShipmentSaveObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ShipmentSaveObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Initializer
     */
    private $initializer;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Initializer $initializer
     */
    public function __construct(CollectionFactory $collectionFactory, Initializer $initializer)
    {
        $this->collectionFactory = $collectionFactory;
        $this->initializer = $initializer;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     *
     * @throws Exception
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        /** @var Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $ceOrder = $this->getOrderExtension($order);

        if (!$ceOrder || !$order->hasDataChanges()) {
            return;
        }

        $this->setContext($order);

        $orderSyncConfig = $this->getOrderSettingsService()->getOrderSyncConfig();
        if ($orderSyncConfig && !$orderSyncConfig->isEnableShipmentInfoSync()) {
            return;
        }

        $createShipmentRequest = $this->getCreateShipmentRequest($order, $shipment);

        $handler = new ShipmentsCreateRequestHandler();
        try {
            $handler->handle($createShipmentRequest);
        } catch (ShipmentRejectedException $e) {
            throw new Exception(__('ChannelEngine status change not allowed.'));
        }
    }

    /**
     * @param Order $order
     * @param Shipment $shipment
     *
     * @return CreateShipmentRequest
     *
     * @throws FailedToRetrieveOrdersChannelSupportEntityException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    private function getCreateShipmentRequest(Order $order, Shipment $shipment): CreateShipmentRequest
    {
        $isPartial = false;
        $track = $shipment->getTracksCollection()->getLastItem();
        $trackTraceNo = $track ? $track->getTrackNumber() : '';

        /** @var ShipmentItemInterface $item */
        foreach ($shipment->getAllItems() as $item) {
            $orderItem = $order->getItemById($item->getOrderItemId());

            if (!$orderItem || $item->getQty() < $orderItem->getQtyOrdered()) {
                $isPartial = true;
                break;
            }
        }

        $shippedItems = $this->getShipmentService()->getAllItems($order->getEntityId());
        $itemInShipment = false;
        $mappings = $this->getMappingsService()->getAttributeMappings();

        if ($this->getSupportCache()->isPartialShipmentSupported($order->getEntityId())) {
            foreach ($shippedItems as $key => $shippedItem) {
                foreach ($shipment->getAllItems() as $item) {
                    $productId = $item->getProductId();

                    if ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_SKU) {
                        $productId = $item->getSku();
                    }

                    if ($shippedItem->getMerchantProductNo() === $productId) {
                        $shippedItem->setQuantity($item->getQty());
                        $itemInShipment = true;
                        break;
                    }
                }

                if (!$itemInShipment) {
                    unset($shippedItems[$key]);
                }

                $itemInShipment = false;
            }
        }

        return new CreateShipmentRequest(
            $order->getEntityId(),
            $shippedItems,
            $isPartial,
            'o_' . $order->getEntityId() . '_s_' . (count($order->getShipmentsCollection()->getItems()) + 1),
            $order->getEntityId(),
            $trackTraceNo,
            '',
            '',
            $track ? $track->getCarrierCode() : ''
        );
    }

    /**
     * @return ShipmentsService
     */
    private function getShipmentService(): ShipmentsService
    {
        return ServiceRegister::getService(ShipmentsService::class);
    }

    /**
     * @param Order $order
     *
     * @return ChannelEngineOrder|null
     */
    private function getOrderExtension(Order $order): ?ChannelEngineOrder
    {
        if (!$order->getEntityId()) {
            return null;
        }

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('order_id', ['in' => [$order->getEntityId()]]);

        $result = $collection->fetchItem();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return !empty($result) ? $result : null;
    }

    /**
     * @param Order $order
     *
     * @return void
     */
    private function setContext(Order $order): void
    {
        ConfigurationManager::getInstance()->setContext($order->getStoreId());
    }

    /**
     * @return OrdersChannelSupportCache
     */
    private function getSupportCache(): OrdersChannelSupportCache
    {
        return ServiceRegister::getService(OrdersChannelSupportCache::class);
    }

    /**
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }
}
