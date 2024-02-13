<?php

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\QueueItemRepository as QueueItemRepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\Priority;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntity;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QueueItemRepository
 *
 * @package ChannelEngine\ChannelEngineIntegration\Repository
 */
class QueueItemRepository extends BaseRepository implements QueueItemRepositoryInterface
{
    /**
     * Fully qualified name of this class.
     */
    public const THIS_CLASS_NAME = __CLASS__;

    /**
     * Name of the base entity table in database.
     */
    public const TABLE_NAME = 'channel_engine_queue';

    /**
     * @inheritDoc
     */
    public function findOldestQueuedItems($priority, $limit = 10): array
    {
        if ($priority !== Priority::NORMAL) {
            return [];
        }

        $queuedItems = [];
        $entity = new $this->entityClass;

        try {
            $records = $this->resourceEntity->findOldestQueuedItems($entity, $limit);
            /** @var QueueItem[] $queuedItems */
            $queuedItems = $this->deserializeEntities($records);
        } catch (LocalizedException $e) {
            // In case of exception return empty result set.
        }

        return $queuedItems;
    }

    /**
     * @inheritDoc
     */
    public function saveWithCondition(QueueItem $queueItem, array $additionalWhere = []): int
    {
        return $this->resourceEntity->saveWithCondition($queueItem, $additionalWhere);
    }

    /**
     * @inheritDoc
     */
    public function batchStatusUpdate(array $ids, $status): void
    {
        if (empty($ids)) {
            return;
        }

        $entity = new $this->entityClass();
        $this->resourceEntity->batchStatusUpdate($ids, $status, $entity);
    }

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return QueueItemEntity::class;
    }

    /**
     * Translates database records to ChannelEngine entities.
     *
     * @param array $records Array of database records.
     *
     * @return Entity[]
     */
    protected function deserializeEntities(array $records): array
    {
        $entities = [];

        foreach ($records as $entity) {
            $item = new QueueItem();
            $item->setId((int)$entity['id']);
            $item->setParentId(!empty($entity['parent_id']) ? (int)$entity['parent_id'] : null);
            $item->setStatus($entity['status']);
            $item->setContext($entity['context']);
            $item->setSerializedTask($entity['serialized_task']);
            $item->setQueueName($entity['queue_name']);
            $item->setLastExecutionProgressBasePoints(!empty($entity['last_execution_progress']) ? (int)$entity['last_execution_progress'] : 0);
            $item->setProgressBasePoints(!empty($entity['progress_base_points']) ? (int)$entity['progress_base_points'] : 0);
            $item->setRetries(!empty($entity['retries']) ? (int)$entity['retries'] : 0);
            $item->setFailureDescription($entity['failure_description']);
            $item->setCreateTimestamp($entity['create_time']);
            $item->setStartTimestamp(($entity['start_time']));
            $item->setEarliestStartTimestamp($entity['earliest_start_time']);
            $item->setQueueTimestamp($entity['queue_time']);
            $item->setLastUpdateTimestamp($entity['last_update_time']);
            $item->setPriority((int)$entity['priority']);

            $entities[] = $item;
        }

        return $entities;
    }
}
