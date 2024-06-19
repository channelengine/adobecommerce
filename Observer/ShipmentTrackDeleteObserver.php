<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\UpdateShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Handlers\ShipmentsUpdateRequestHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrder;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder\CollectionFactory;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Exception;
use Magento\Backend\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;

/**
 * Class ShipmentTrackDeleteObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ShipmentTrackDeleteObserver implements ObserverInterface
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
     * @var Session
     */
    private $session;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Session $session
     * @param Initializer $initializer
     */
    public function __construct(CollectionFactory $collectionFactory, Session $session, Initializer $initializer)
    {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->initializer = $initializer;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        /** @var Track $track */
        $track = $observer->getEvent()->getTrack();
        $shipment = $track->getShipment();
        $ceOrder = $shipment ? $this->getOrderExtension($shipment->getOrder()) : null;

        if (!$shipment || !$ceOrder) {
            return;
        }

        $this->setContext($track);

        $orderConfig = $this->getOrderSettingsService()->getOrderSyncConfig();

        if (!$orderConfig || !$orderConfig->isEnableOrderCancellationSync()) {
            return;
        }

        $updateShipmentRequest = $this->getUpdateShipmentRequest($track, $shipment);

        $handler = new ShipmentsUpdateRequestHandler();
        try {
            $handler->handle($updateShipmentRequest);
        } catch (BaseException $e) {
            $this->session->setChannelEngineNotification(__('ChannelEngine status change not allowed.'));
        }
    }

    /**
     * @param Track $track
     * @param Shipment $shipment
     *
     * @return UpdateShipmentRequest
     */
    private function getUpdateShipmentRequest(Track $track, Shipment $shipment): UpdateShipmentRequest
    {
        /** @var Track[] $allTracks */
        $allTracks = $shipment->getAllTracks();
        $lastTrack = $allTracks[count($allTracks) - 1] ?? null;

        if ($lastTrack && $lastTrack->getEntityId() === $track->getEntityId()) {
            $lastTrack = $allTracks[count($allTracks) - 2] ?? null;
        }


        $trackNumber = $lastTrack ? $lastTrack->getTrackNumber() : '';
        $order = $shipment->getOrder();
        $isPartial = false;

        /** @var ShipmentItemInterface $item */
        foreach ($shipment->getAllItems() as $item) {
            $orderItem = $order->getItemById($item->getOrderItemId());

            if (!$orderItem || $item->getQty() < $orderItem->getQtyOrdered()) {
                $isPartial = true;
                break;
            }
        }

        return new UpdateShipmentRequest(
            $shipment->getOrderId(),
            $isPartial,
            $lastTrack ? $lastTrack->getCarrierCode() : '',
            $trackNumber,
            '',
            ''
        );
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
     * @param Track $track
     *
     * @return void
     */
    private function setContext(Track $track): void
    {
        ConfigurationManager::getInstance()->setContext($track->getStoreId());
    }

    /**
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }
}
