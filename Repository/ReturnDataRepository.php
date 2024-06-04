<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnData;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnDataFactory;

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
     * ProductEventRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setResourceEntityObject($this->getReturnDataFactory()->create());
        $this->getResourceEntityObject()->setTableName(self::TABLE_NAME);
    }

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return ReturnData::class;
    }

    /**
     * @return ReturnDataFactory
     */
    private function getReturnDataFactory(): ReturnDataFactory
    {
        return ServiceRegister::getService(ReturnDataFactory::class);
    }
}
