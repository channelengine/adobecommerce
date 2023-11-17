<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ReturnData
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class ReturnData extends AbstractModel
{
    /**
     * Model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ReturnData::class);
    }
}
