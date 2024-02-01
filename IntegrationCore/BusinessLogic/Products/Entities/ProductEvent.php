<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;

/**
 * Class ProductEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities
 */
class ProductEvent extends Entity
{
    const CLASS_NAME = __CLASS__;

    const DELETED = 'DELETED';
    const UPSERT = 'UPSERT';
    const PURGED = 'PURGED';
    const REPLACED = 'REPLACED';

    protected $fields = ['id', 'type', 'productId'];

    /**
     * @var string one of ['DELETED', 'UPSERT', 'PURGED', 'REPLACED']
     */
    protected $type;
    /**
     * @var string
     */
    protected $productId;

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
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function getConfig()
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('type')
            ->addStringIndex('productId');

        return new EntityConfiguration($indexMap, 'ProductEvent');
    }
}