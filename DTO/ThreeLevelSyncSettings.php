<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class AttributeMappings
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class ThreeLevelSyncSettings extends DataTransferObject
{
    /**
     * @var bool
     */
    private $enableThreeLevelSync;
    /**
     * @var string
     */
    private $syncAttribute;
    /**
     * @var bool
     */
    private $attributeDeleted;

    /**
     * @param bool $enableThreeLevelSync
     * @param string $syncAttribute
     * @param bool $attributeDeleted
     */
    public function __construct(
        bool $enableThreeLevelSync,
        string $syncAttribute,
        bool $attributeDeleted
    ) {
        $this->enableThreeLevelSync = $enableThreeLevelSync;
        $this->syncAttribute = $syncAttribute;
        $this->attributeDeleted = $attributeDeleted;
    }

    /**
     * @return bool
     */
    public function getEnableThreeLevelSync(): bool
    {
        return $this->enableThreeLevelSync;
    }

    /**
     * @return string
     */
    public function getSyncAttribute(): string
    {
        return $this->syncAttribute;
    }

    /**
     * @return bool
     */
    public function getAttributeDeleted(): bool
    {
        return $this->attributeDeleted;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'enableThreeLevelSync' => $this->enableThreeLevelSync,
            'syncAttribute' => $this->syncAttribute,
            'attributeDeleted' => $this->attributeDeleted
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): ThreeLevelSyncSettings
    {
        return new self(
            static::getDataValue($data, 'enableThreeLevelSync'),
            static::getDataValue($data, 'syncAttribute'),
            static::getDataValue($data, 'attributeDeleted')
        );
    }
}
