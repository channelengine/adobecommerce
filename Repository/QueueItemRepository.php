<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\QueueItemRepository as QueueItemRepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\Priority;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntity;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntityFactory;
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
     * QueueItemRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setResourceEntityObject($this->getQueueItemFactory()->create());
        $this->getResourceEntityObject()->setTableName(self::TABLE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function findOldestQueuedItems($priority, $limit = 10): array
    {
        if ($priority !== Priority::NORMAL) {
            return [];
        }

        $queuedItems = [];
        $entityClass = $this->getEntityClass();
        $entity = new $entityClass;

        try {
            $entityObject = $this->getResourceEntityObject();
            $records = $entityObject->findOldestQueuedItems($entity, $limit);
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
        /** @var QueueItemEntity $entityObject */
        $entityObject = $this->getResourceEntityObject();

        return $entityObject->saveWithCondition($queueItem, $additionalWhere);
    }

    /**
     * @inheritDoc
     */
    public function batchStatusUpdate(array $ids, $status): void
    {
        if (empty($ids)) {
            return;
        }

        $entityClass = $this->getEntityClass();
        $entity = new $entityClass;
        $this->getResourceEntityObject()->batchStatusUpdate($ids, $status, $entity);
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

    /**
     * @return QueueItemEntityFactory
     */
    private function getQueueItemFactory(): QueueItemEntityFactory
    {
        return ServiceRegister::getService(QueueItemEntityFactory::class);
    }
}
