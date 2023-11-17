<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class StockSettings
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class StockSettings extends DataTransferObject
{
    /**
     * @var bool
     */
    private $enableStockSync;

    /**
     * @var string[]
     */
    private $inventories;
    /**
     * @var int
     */
    private $quantity;

    /**
     * @param bool $enableStockSync
     * @param array $inventories
     * @param int $quantity
     */
    public function __construct(bool $enableStockSync, array $inventories, int $quantity)
    {
        $this->enableStockSync = $enableStockSync;
        $this->inventories = $inventories;
        $this->quantity = $quantity;
    }

    /**
     * @return bool
     */
    public function isEnableStockSync(): bool
    {
        return $this->enableStockSync;
    }

    /**
     * @return string[]
     */
    public function getInventories(): array
    {
        return $this->inventories;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'enableStockSync' => $this->enableStockSync,
            'inventories' => $this->inventories,
            'quantity' => $this->quantity,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): StockSettings
    {
        return new self(
            static::getDataValue($data, 'enableStockSync'),
            static::getDataValue($data, 'inventories'),
            static::getDataValue($data, 'quantity')
        );
    }
}
