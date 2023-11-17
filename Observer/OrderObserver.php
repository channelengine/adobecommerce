<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\Exceptions\CancellationRejectedException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Handlers\CancellationRequestHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrder;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder as ResourceModel;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder\CollectionFactory;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

/**
 * Class OrderObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class OrderObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ResourceModel
     */
    private $resource;
    /**
     * @var Initializer
     */
    private $initializer;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ResourceModel $resource
     * @param Initializer $initializer
     */
    public function __construct(CollectionFactory $collectionFactory, ResourceModel $resource, Initializer $initializer)
    {
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->initializer = $initializer;
    }

    /**
     * Handles order save event.
     *
     * @param Observer $observer
     *
     * @return void
     *
     * @throws Exception
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        /** @var Order $order */
        $order = $observer->getData('order');
        $ceOrder = $this->getOrderExtension($order);

        if (!$ceOrder || !$order->hasDataChanges() || $ceOrder->getOrderCanceled()) {
            return;
        }

        $this->setContext($order);

        $orderConfig = $this->getOrderSettingsService()->getOrderSyncConfig();

        if (!$orderConfig || !$orderConfig->isEnableOrderCancellationSync()) {
            return;
        }

        $cancelledItems = $this->getCancellationItems($order);

        if (empty($cancelledItems)) {
            return;
        }

        $cancellationRequest = new CancellationRequest(
            $order->getEntityId(),
            $order->getEntityId(),
            $cancelledItems,
            $order->getTotalCanceled() === $order->getTotalInvoiced(),
            CancellationRequest::REASON_OTHER
        );

        $handler = new CancellationRequestHandler();

        try {
            $handler->handle($cancellationRequest, $order->getEntityId());
            $ceOrder->setOrderCanceled(true);
            $this->resource->save($ceOrder);
        } catch (CancellationRejectedException $e) {
            throw new Exception(__('ChannelEngine status change not allowed.'));
        }
    }

    /**
     * @param Order $order
     *
     * @return CancellationItem[]
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getCancellationItems(Order $order): array
    {
        $lineItems = [];
        $mappings = $this->getMappingsService()->getAttributeMappings();

        foreach ($order->getItems() as $item) {
            if ($item->getQtyCanceled() > 0) {
                $lineItems[] = new CancellationItem(
                    ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID) ?
                        $item->getProductId() : $item->getSku(),
                    (int)$item->getQtyCanceled(),
                    true
                );
            }
        }

        return $lineItems;
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
