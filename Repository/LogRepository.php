<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntity;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntityFactory;

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
     * ProductEventRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setResourceEntityObject($this->getLogEntityFactory()->create());
        $this->getResourceEntityObject()->setTableName(self::TABLE_NAME);
    }

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return LogEntity::class;
    }

    /**
     * @return LogEntityFactory
     */
    private function getLogEntityFactory(): LogEntityFactory
    {
        return ServiceRegister::getService(LogEntityFactory::class);
    }
}
