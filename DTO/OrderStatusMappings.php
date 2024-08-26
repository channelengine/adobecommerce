<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class OrderStatusMappings
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class OrderStatusMappings extends DataTransferObject
{
    public const DEFAULT_INCOMING_ORDER_STATUS = 'processing';
    public const DEFAULT_SHIPPED_ORDER_STATUS = 'complete';
    public const DEFAULT_FULFILLED_ORDER_STATUS = 'complete';

    /**
     * @var string
     */
    private $statusOfIncomingOrders;
    /**
     * @var string
     */
    private $statusOfShippedOrders;
    /**
     * @var string
     */
    private $statusOfFulfilledOrders;

    /**
     * @param string $statusOfIncomingOrders
     * @param string $statusOfShippedOrders
     * @param string $statusOfFulfilledOrders
     */
    public function __construct(
        string $statusOfIncomingOrders,
        string $statusOfShippedOrders,
        string $statusOfFulfilledOrders
    ) {
        $this->statusOfIncomingOrders = $statusOfIncomingOrders;
        $this->statusOfShippedOrders = $statusOfShippedOrders;
        $this->statusOfFulfilledOrders = $statusOfFulfilledOrders;
    }

    /**
     * @return string
     */
    public function getStatusOfIncomingOrders(): string
    {
        return $this->statusOfIncomingOrders;
    }

    /**
     * @return string
     */
    public function getStatusOfShippedOrders(): string
    {
        return $this->statusOfShippedOrders;
    }

    /**
     * @return string
     */
    public function getStatusOfFulfilledOrders(): string
    {
        return $this->statusOfFulfilledOrders;
    }

    /**
     * @param string $statusOfIncomingOrders
     * @return void
     */
    public function setStatusOfIncomingOrders(string $statusOfIncomingOrders): void
    {
        $this->statusOfIncomingOrders = $statusOfIncomingOrders;
    }

    /**
     * @param string $statusOfShippedOrders
     * @return void
     */
    public function setStatusOfShippedOrders(string $statusOfShippedOrders): void
    {
        $this->statusOfShippedOrders = $statusOfShippedOrders;
    }

    /**
     * @param string $statusOfFulfilledOrders
     * @return void
     */
    public function setStatusOfFulfilledOrders(string $statusOfFulfilledOrders): void
    {
        $this->statusOfFulfilledOrders = $statusOfFulfilledOrders;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'statusOfIncomingOrders' => $this->statusOfIncomingOrders,
            'statusOfShippedOrders' => $this->statusOfShippedOrders,
            'statusOfFulfilledOrders' => $this->statusOfFulfilledOrders,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): OrderStatusMappings
    {
        return new self(
            static::getDataValue($data, 'statusOfIncomingOrders', static::DEFAULT_INCOMING_ORDER_STATUS),
            static::getDataValue($data, 'statusOfShippedOrders', static::DEFAULT_SHIPPED_ORDER_STATUS),
            static::getDataValue($data, 'statusOfFulfilledOrders', static::DEFAULT_FULFILLED_ORDER_STATUS)
        );
    }
}
