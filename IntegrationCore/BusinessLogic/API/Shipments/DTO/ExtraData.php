<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ExtraData
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class ExtraData extends DataTransferObject
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'Key' => $this->key,
            'Value' => $this->value,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $extraData = new self();
        $extraData->setKey(static::getDataValue($data, 'Key'));
        $extraData->setValue(static::getDataValue($data, 'Value'));

        return $extraData;
    }
}