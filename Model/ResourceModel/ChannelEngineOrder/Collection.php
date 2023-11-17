<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder;

use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrder;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder as ResourceModelChannelEngineOrder;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder
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
        $this->_init(ChannelEngineOrder::class, ResourceModelChannelEngineOrder::class);
    }
}
