<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ReturnsSettings
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class ReturnsSettings extends DataTransferObject
{
    /**
     * @var bool
     */
    private $returnsEnabled;
    /**
     * @var string
     */
    private $defaultCondition;
    /**
     * @var string
     */
    private $defaultResolution;

    /**
     * @param bool $returnsEnabled
     * @param string $defaultCondition
     * @param string $defaultResolution
     */
    public function __construct(bool $returnsEnabled, string $defaultCondition, string $defaultResolution)
    {
        $this->returnsEnabled = $returnsEnabled;
        $this->defaultCondition = $defaultCondition;
        $this->defaultResolution = $defaultResolution;
    }

    /**
     * @return string
     */
    public function getDefaultCondition(): string
    {
        return $this->defaultCondition;
    }

    /**
     * @param string $defaultCondition
     */
    public function setDefaultCondition(string $defaultCondition): void
    {
        $this->defaultCondition = $defaultCondition;
    }

    /**
     * @return string
     */
    public function getDefaultResolution(): string
    {
        return $this->defaultResolution;
    }

    /**
     * @param string $defaultResolution
     */
    public function setDefaultResolution(string $defaultResolution): void
    {
        $this->defaultResolution = $defaultResolution;
    }

    /**
     * @return bool
     */
    public function isReturnsEnabled(): bool
    {
        return $this->returnsEnabled;
    }

    /**
     * @param bool $returnsEnabled
     */
    public function setReturnsEnabled(bool $returnsEnabled): void
    {
        $this->returnsEnabled = $returnsEnabled;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'returnsEnabled' => $this->returnsEnabled,
            'defaultCondition' => $this->defaultCondition,
            'defaultResolution' => $this->defaultResolution,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): ReturnsSettings
    {
        return new self(
            static::getDataValue($data, 'returnsEnabled'),
            static::getDataValue($data, 'defaultCondition'),
            static::getDataValue($data, 'defaultResolution')
        );
    }
}
