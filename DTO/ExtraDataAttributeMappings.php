<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ExtraDataAttributeMappings
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class ExtraDataAttributeMappings extends DataTransferObject
{
    /**
     * @var array
     */
    private $mappings;

    /**
     * @param array $mappings
     */
    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * @return array
     */
    public function getMappings(): array
    {
        return $this->mappings;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'extraDataAttributeMappings' => $this->mappings,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): ExtraDataAttributeMappings
    {
        return new self(static::getDataValue($data, 'extraDataAttributeMappings'));
    }
}
