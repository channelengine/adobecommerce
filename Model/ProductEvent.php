<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ProductEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class ProductEvent extends AbstractModel
{
    /**
     * Model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ProductEvent::class);
    }
}
