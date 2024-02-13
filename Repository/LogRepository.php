<?php

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntity;

/**
 * Class LogRepository
 *
 * @package ChannelEngine\ChannelEngineIntegration\Repository
 */
class LogRepository extends BaseRepository
{
    /**
     * Fully qualified name of this class.
     */
    public const THIS_CLASS_NAME = __CLASS__;
    /**
     * Name of the base entity table in database.
     */
    public const TABLE_NAME = 'channel_engine_logs';

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return LogEntity::class;
    }
}
