<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineEntity;

use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineEntity;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineEntity as ChannelEngineResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineEntity
 */
class Collection extends AbstractCollection
{
    /**
     * Collection initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ChannelEngineEntity::class, ChannelEngineResourceModel::class);
    }
}
