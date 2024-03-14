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
     * @var bool
     */
    private $enableMSI;

    /**
     * @param bool $enableStockSync
     * @param array $inventories
     * @param int $quantity
     * @param bool $enableMSI
     */
    public function __construct(bool $enableStockSync, array $inventories, int $quantity, bool $enableMSI)
    {
        $this->enableStockSync = $enableStockSync;
        $this->inventories = $inventories;
        $this->quantity = $quantity;
        $this->enableMSI = $enableMSI;
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
     * @return bool
     */
    public function isEnableMSI(): bool
    {
        return $this->enableMSI;
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
            'enableMSI' => $this->enableMSI,
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
            static::getDataValue($data, 'quantity'),
            static::getDataValue($data, 'enableMSI')
        );
    }
}
