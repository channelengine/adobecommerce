<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Configuration;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;

/**
 * Class ReturnsConfigEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Configuration
 */
class ReturnsConfigEntity extends Entity
{
    const CLASS_NAME = __CLASS__;

    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $value;
    /**
     * @var array
     */
    protected $fields = ['id', 'key', 'value'];

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $map = new IndexMap();
        $map->addStringIndex('key');

        return new EntityConfiguration($map, 'ReturnsConfigEntity');
    }

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
}