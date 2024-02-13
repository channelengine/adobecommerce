<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnData;

use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnData as ReturnDataResourceModel;
use ChannelEngine\ChannelEngineIntegration\Model\ReturnData;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnData
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
        $this->_init(ReturnData::class, ReturnDataResourceModel::class);
    }
}
