<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ExtraData
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO
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
     * @var string One of [ TEXT, NUMBER, URL, IMAGEURL ]
     */
    protected $type;
    /**
     * @var bool
     */
    protected $isPublic;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $entity = new static();
        $entity->setKey(static::getDataValue($data, 'Key', null));
        $entity->setValue(static::getDataValue($data, 'Value', null));
        $entity->setType(static::getDataValue($data, 'Type'));
        $entity->setIsPublic(static::getDataValue($data, 'IsPublic', false));

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'Key' => $this->getKey(),
            'Value' => $this->getValue(),
            'Type' => $this->getType(),
            'IsPublic' => $this->isPublic(),
        ];
    }
}