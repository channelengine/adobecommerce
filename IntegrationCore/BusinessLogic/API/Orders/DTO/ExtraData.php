<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ExtraData
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO
 */
class ExtraData extends DataTransferObject
{
    /**
     * @var string | null
     */
    protected $key;
    /**
     * @var string | null
     */
    protected $value;

    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string|null $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $extraData = new static();

        $extraData->setKey(static::getDataValue($data, 'Key', null));
        $extraData->setValue(static::getDataValue($data, 'Value', null));

        return $extraData;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'Key' => $this->getKey(),
            'Value' => $this->getValue(),
        ];
    }
}