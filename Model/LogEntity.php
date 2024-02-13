<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class LogEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class LogEntity extends AbstractModel
{
    /**
     * Model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\LogEntity::class);
    }
}
