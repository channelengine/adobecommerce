<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;

/**
 * Class OrdersChannelSupportEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport
 */
class OrdersChannelSupportEntity extends Entity
{
    const CLASS_NAME = __CLASS__;

    /**
     * @var string
     */
    protected $shopOrderId;
    /**
     * @var string
     */
    protected $channelEngineSupport;
    /**
     * Array of field names.
     *
     * @var array
     */
    protected $fields = array('id', 'shopOrderId', 'channelEngineSupport');

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $map = new IndexMap();
        $map->addStringIndex('shopOrderId')
            ->addStringIndex('channelEngineSupport');

        return new EntityConfiguration($map, 'OrdersChannelSupportEntity');
    }

    /**
     * @return string
     */
    public function getShopOrderId()
    {
        return $this->shopOrderId;
    }

    /**
     * @param string $shopOrderId
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->shopOrderId = $shopOrderId;
    }

    /**
     * @return string
     */
    public function getChannelEngineSupport()
    {
        return $this->channelEngineSupport;
    }

    /**
     * @param string $channelEngineSupport
     */
    public function setChannelEngineSupport($channelEngineSupport)
    {
        $this->channelEngineSupport = $channelEngineSupport;
    }
}
