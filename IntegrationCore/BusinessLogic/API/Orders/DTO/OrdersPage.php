<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class OrdersPage
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO
 */
class OrdersPage extends DataTransferObject
{
    /**
     * @var Order[]
     */
    protected $orders;
    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $orders = [];

        foreach ($this->getOrders() as $order) {
            $orders[] = $order->toArray();
        }

        return [
            'Content' => $orders,
            'TotalCount' => $this->getTotalCount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $ordersPage = new self();
        $ordersPage->setTotalCount(static::getDataValue($data, 'TotalCount', null));
        $ordersPage->setOrders(Order::fromBatch($data['Content']));

        return $ordersPage;
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param Order[] $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }
}