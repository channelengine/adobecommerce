<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class PriceSettingsEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class PriceSettings extends DataTransferObject
{
    /**
     * Fully qualified name of this class.
     */
    public const CLASS_NAME = __CLASS__;
    /**
     * @var bool
     */
    private $groupPricing;
    /**
     * @var int
     */
    private $customerGroup;
    /**
     * @var string
     */
    private $priceAttribute;
    /**
     * @var int
     */
    private $quantity;

    /**
     * @param bool $groupPricing
     * @param string $priceAttribute
     * @param int $customerGroup
     * @param int $quantity
     */
    public function __construct(bool $groupPricing, string $priceAttribute, int $customerGroup, int $quantity)
    {
        $this->groupPricing = $groupPricing;
        $this->priceAttribute = $priceAttribute;
        $this->customerGroup = $customerGroup;
        $this->quantity = $quantity;
    }

    /**
     * @return bool
     */
    public function isGroupPricing(): bool
    {
        return $this->groupPricing;
    }

    /**
     * @return string
     */
    public function getPriceAttribute(): string
    {
        return $this->priceAttribute;
    }

    /**
     * @return int
     */
    public function getCustomerGroup(): int
    {
        return $this->customerGroup;
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
            'groupPricing' => $this->groupPricing,
            'priceAttribute' => $this->priceAttribute,
            'customerGroup' => $this->customerGroup,
            'quantity' => $this->quantity,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): PriceSettings
    {
        return new self(
            static::getDataValue($data, 'groupPricing', false),
            static::getDataValue($data, 'priceAttribute', 0),
            static::getDataValue($data, 'customerGroup', 0),
            static::getDataValue($data, 'quantity', 0)
        );
    }
}
