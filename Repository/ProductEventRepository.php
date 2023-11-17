<?php

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Repositories\ProductEventRepository as BaseEventRepository;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent as ProductEventEntity;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProductEventRepository
 *
 * @package ChannelEngine\ChannelEngineIntegration\Repository
 */
class ProductEventRepository extends BaseRepository implements BaseEventRepository
{
    /**
     * Fully qualified name of this class.
     */
    public const THIS_CLASS_NAME = __CLASS__;
    /**
     * Name of the base entity table in database.
     */
    public const TABLE_NAME = 'channel_engine_events';

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return ProductEvent::class;
    }

    /**
     * Retrieves all unique contexts from events table.
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getContexts(): array
    {
        return $this->resourceEntity->getContexts();
    }

    /**
     * Deletes multiple entities and returns success flag.
     *
     * @param ProductEventEntity[] $entities
     *
     * @return bool TRUE if operation succeeded; otherwise, FALSE.
     */
    public function batchDelete(array $entities): bool
    {
        return $this->resourceEntity->batchDelete($entities);
    }
}
