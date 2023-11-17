<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEvent;

use ChannelEngine\ChannelEngineIntegration\Model\ProductEvent;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEvent as ProductEventResourceModel;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEvent
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
        $this->_init(ProductEvent::class, ProductEventResourceModel::class);
    }
}
