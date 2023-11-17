<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ChannelEngineEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class ChannelEngineEntity extends AbstractModel
{
    /**
     * Model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ChannelEngineEntity::class);
    }
}
