<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ShipmentRejectedException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts\ShipmentsService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\CreateShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\OrderItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\RejectResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\UpdateShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrder;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder\CollectionFactory;
use Exception;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ShipmentsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ShipmentsService implements BaseService
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(OrderRepositoryInterface $orderRepository, CollectionFactory $collectionFactory)
    {
        $this->orderRepository = $orderRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieves all order items by order id.
     *
     * @param string $shopOrderId
     *
     * @return OrderItem[]
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAllItems($shopOrderId): array
    {
        $order = $this->orderRepository->get($shopOrderId);
        $ceOrder = $this->getCEOrder($shopOrderId);

        if (!$ceOrder) {
            return [];
        }

        $items = [];
        $mappings = $this->getMappingsService()->getAttributeMappings();

        foreach ($order->getItems() as $item) {
            $orderItem = new OrderItem();
            $orderItem->setShipped((int)$item->getQtyShipped() === (int)$item->getQtyOrdered());
            $orderItem->setQuantity((int)$item->getQtyShipped());
            $orderItem->setMerchantProductNo(
                ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID) ?
                    $item->getProductId() : $item->getSku()
            );

            $items[] = $orderItem;
        }

        return $items;
    }

    /**
     * Rejects creation request.
     *
     * @param CreateShipmentRequest $request
     * @param Exception $reason
     *
     * @return RejectResponse
     *
     * @throws ShipmentRejectedException
     */
    public function rejectCreate($request, Exception $reason): RejectResponse
    {
        $error = json_decode($reason->getMessage(), true);
        throw new ShipmentRejectedException(__('Shipment creation failed. Reason: ') . $error['message']);
    }

    /**
     * Rejects update request.
     *
     * @param UpdateShipmentRequest $request
     * @param Exception $reason
     *
     * @return RejectResponse
     *
     * @throws ShipmentRejectedException
     */
    public function rejectUpdate($request, Exception $reason): RejectResponse
    {
        $error = json_decode($reason->getMessage(), true);
        throw new ShipmentRejectedException(__('Shipment update failed. Reason: ') . $error['message']);
    }

    /**
     * @param string $shopOrderId
     *
     * @return ChannelEngineOrder|null
     */
    private function getCEOrder(string $shopOrderId): ?ChannelEngineOrder
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('order_id', ['in' => [$shopOrderId]]);

        $result = $collection->fetchItem();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return !empty($result) ? $result : null;
    }

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }
}
