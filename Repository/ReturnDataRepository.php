<?php

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnData;

/**
 * Class ReturnDataRepository
 *
 * @package ChannelEngine\ChannelEngineIntegration\Repository
 */
class ReturnDataRepository extends BaseRepository
{
    /**
     * Fully qualified name of this class.
     */
    public const THIS_CLASS_NAME = __CLASS__;
    /**
     * Name of the base entity table in database.
     */
    public const TABLE_NAME = 'channel_engine_returns';

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return ReturnData::class;
    }
}
