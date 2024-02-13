<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ChannelEngineOrder
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class ChannelEngineOrder extends AbstractModel
{
    /**
     * Model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ChannelEngineOrder::class);
    }
}
