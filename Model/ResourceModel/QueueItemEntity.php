<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Utility\IndexHelper;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueItemSaveException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use Exception;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QueueItemEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel
 */
class QueueItemEntity extends ChannelEngineEntity
{
    /**
     * Finds list of earliest queued queue items per queue. Following list of criteria for searching must be satisfied:
     *      - Queue must be without already running queue items
     *      - For one queue only one (oldest queued) item should be returned
     *
     * @param int $limit Result set limit. By default max 10 earliest queue items will be returned
     *
     * @param Entity $entity ChannelEngine entity.
     *
     * @return QueueItem[] Found queue item list
     *
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     */
    public function findOldestQueuedItems(Entity $entity, int $limit = 10): array
    {
        $runningQueueNames = $this->getRunningQueueNames($entity);

        return $this->getQueuedItems($runningQueueNames, $limit);
    }

    /**
     * Updates status of a batch of queue items.
     *
     * @param array $ids
     * @param string $status
     * @param Entity $entity
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function batchStatusUpdate(array $ids, string $status, Entity $entity): void
    {
        $connection = $this->getConnection();
        $fieldIndexMap = IndexHelper::mapFieldsToIndexes($entity);
        $statusIndex = 'index_' . $fieldIndexMap['status'];
        $lastUpdateTime = 'index_' . $fieldIndexMap['lastUpdateTimestamp'];
        $currentTime = $this->getTimeProvider()->getCurrentLocalTime();
        $currentTimestamp = $currentTime->getTimestamp();
        $where = $this->getIdFieldName() . " in (" . implode(',', $ids) . ")";

        $connection->update(
            $this->getMainTable(),
            [
                $statusIndex => $status,
                'status' => $status,
                $lastUpdateTime => IndexHelper::castFieldValue($currentTimestamp, 'integer'),
                'last_update_time' => IndexHelper::castFieldValue($currentTimestamp, 'integer')
            ],
            $where
        );
    }

    /**
     * Returns names of queues containing items that are currently in progress.
     *
     * @param Entity $entity ChannelEngine entity.
     *
     * @return array Names of queues containing items that are currently in progress.
     *
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     */
    private function getRunningQueueNames(Entity $entity): array
    {
        $filter = new QueryFilter();
        $filter->where('status', Operators::EQUALS, QueueItem::IN_PROGRESS);

        /** @var QueueItem[] $runningQueueItems */
        $runningQueueItems = $this->selectEntities($entity, $filter);
        $fieldIndexMap = IndexHelper::mapFieldsToIndexes($entity);
        $queueNameIndex = 'index_' . $fieldIndexMap['queueName'];

        return array_map(
            function ($runningQueueItem) use ($queueNameIndex) {
                return $runningQueueItem[$queueNameIndex];
            },
            $runningQueueItems
        );
    }

    /**
     * Returns all queued items.
     *
     * @param array $runningQueueNames Array of queues containing items that are currently in progress.
     * @param int $limit Maximum number of records that can be retrieved.
     *
     * @return array Array of queued items.
     *
     * @throws LocalizedException
     */
    private function getQueuedItems(array $runningQueueNames, int $limit): array
    {
        $queueNameIndex = $this->getIndexMapping('queueName', QueueItem::getClassName());

        $condition = $this->buildWhereString(
            [
                'type' => 'QueueItem',
                $this->getIndexMapping('status', QueueItem::getClassName()) => QueueItem::QUEUED,
            ]
        );

        if (!empty($runningQueueNames)) {
            $quotedNames = array_map(
                function ($name) {
                    return $this->getConnection()->quote($name);
                },
                $runningQueueNames
            );

            $condition .= sprintf(' AND ' . $queueNameIndex . ' NOT IN (%s)', implode(', ', $quotedNames));
        }

        $query = 'SELECT *'
            . 'FROM ( '
            . 'SELECT ' . $queueNameIndex . ', MIN(id) AS id '
            . 'FROM ' . $this->getMainTable() . ' '
            . 'WHERE ' . $condition . ' '
            . 'GROUP BY ' . $queueNameIndex . ' '
            . 'LIMIT ' . $limit
            . ' ) AS queueView '
            . 'INNER JOIN ' . $this->getMainTable() . ' AS queueTable '
            . 'ON queueView.id = queueTable.id';

        $records = $this->getConnection()->fetchAll($query);

        return \is_array($records) ? $records : [];
    }

    /**
     * Builds where condition string based on given key/value parameters.
     *
     * @param array $whereFields Key value pairs of where condition
     *
     * @return string Properly sanitized where condition string
     */
    private function buildWhereString(array $whereFields = []): string
    {
        $where = [];
        foreach ($whereFields as $field => $value) {
            $where[] = $field . Operators::EQUALS . $this->getConnection()->quote($value);
        }

        return implode(' AND ', $where);
    }

    /**
     * Creates or updates given queue item. If queue item id is not set, new queue item will be created otherwise
     * update will be performed.
     *
     * @param QueueItem $queueItem Item to save
     * @param array $additionalWhere List of key/value pairs that must be satisfied upon saving queue item. Key is
     *  queue item property and value is condition value for that property. Example for MySql storage:
     *  $storage->save($queueItem, array('status' => 'queued')) should produce query
     *  UPDATE queue_storage_table SET .... WHERE .... AND status => 'queued'
     *
     * @return int Id of saved queue item
     *
     * @throws QueueItemSaveException if queue item could not be saved
     */
    public function saveWithCondition(QueueItem $queueItem, array $additionalWhere = []): int
    {
        $savedItemId = null;

        try {
            $itemId = $queueItem->getId();
            if ($itemId === null || $itemId <= 0) {
                $savedItemId = $this->saveEntity($queueItem);
            } else {
                $this->updateQueueItem($queueItem, $additionalWhere);
            }
        } catch (Exception $e) {
            throw new QueueItemSaveException('Failed to save queue item.', 0, $e);
        }

        return $savedItemId ?: $itemId;
    }

    /**
     * Updates queue item.
     *
     * @param QueueItem $queueItem Queue item entity.
     * @param array $additionalWhere Array of additional where conditions.
     *
     * @throws QueueItemSaveException
     * @throws QueryFilterInvalidParamException
     * @throws LocalizedException
     */
    private function updateQueueItem(QueueItem $queueItem, array $additionalWhere): void
    {
        $filter = new QueryFilter();
        $filter->where('id', Operators::EQUALS, $queueItem->getId());

        foreach ($additionalWhere as $name => $value) {
            if ($value === null) {
                $filter->where($name, Operators::NULL);
            } else {
                $filter->where($name, Operators::EQUALS, $value);
            }
        }

        $filter->setLimit(1);
        $results = $this->selectEntities(new QueueItem(), $filter);
        if (empty($results)) {
            throw new QueueItemSaveException("Can not update queue item with id {$queueItem->getId()}.");
        }

        $this->updateEntity($queueItem);
    }

    /**
     * Prepares data for inserting a new record or updating an existing one.
     *
     * @param Entity $entity ChannelEngine entity object.
     * @param array $indexes Array of index values.
     *
     * @return array Prepared record for inserting or updating.
     */
    protected function prepareDataForInsertOrUpdate(Entity $entity, array $indexes): array
    {
        /** @var QueueItem $item */
        $item = $entity;

        $storageItem = [
            'type' => $item->getConfig()->getType(),
            'parent_id' => $item->getParentId(),
            'status' => $item->getStatus(),
            'context' => $item->getContext(),
            'serialized_task' => $item->getSerializedTask(),
            'queue_name' => $item->getQueueName(),
            'last_execution_progress' => $item->getLastExecutionProgressBasePoints(),
            'progress_base_points' => $item->getProgressBasePoints(),
            'retries' => $item->getRetries(),
            'failure_description' => $item->getFailureDescription(),
            'create_time' => $item->getCreateTimestamp(),
            'start_time' => $item->getStartTimestamp(),
            'earliest_start_time' => $item->getEarliestStartTimestamp(),
            'queue_time' => $item->getQueueTimestamp(),
            'last_update_time' => $item->getLastUpdateTimestamp(),
            'priority' => $item->getPriority(),
        ];

        foreach ($indexes as $index => $value) {
            $storageItem['index_' . $index] = $value;
        }

        return $storageItem;
    }


    /**
     * Provides time provider.
     *
     * @return TimeProvider
     */
    private function getTimeProvider(): TimeProvider
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
}
