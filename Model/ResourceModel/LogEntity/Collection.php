<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntity;

use ChannelEngine\ChannelEngineIntegration\Model\LogEntity;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntity as LogEntityResourceModel;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntity
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
        $this->_init(LogEntity::class, LogEntityResourceModel::class);
    }
}
