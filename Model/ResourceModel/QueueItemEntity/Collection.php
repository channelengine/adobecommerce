<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntity;

use ChannelEngine\ChannelEngineIntegration\Model\QueueItemEntity;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntity as QueueItemResourceModel;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntity
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
        $this->_init(QueueItemEntity::class, QueueItemResourceModel::class);
    }
}
