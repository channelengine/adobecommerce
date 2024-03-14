<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Enums\Status;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueItemDeserializationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;

class TransactionLogService implements Contracts\TransactionLogService
{
    /**
     * @inheritDoc
     *
     * @throws QueueItemDeserializationException
     */
    public function create(QueueItem $item)
    {
        /** @var TransactionalTask $task */
        $task = $item->getTask();
        if ($task === null) {
            return;
        }

        if (!($task instanceof Contracts\TransactionLogAware)) {
            return;
        }

        if ($item->getParentId() !== null) {
            return;
        }

        if (($log = $this->getLatestTransactionLog($item->getId())) !== null) {
            $this->updateStatus($log, Status::FAILED);
        }

        $transactionLog = new TransactionLog();
        $transactionLog->setExecutionId($item->getId());
        $transactionLog->setStatus(Status::QUEUED);
        $transactionLog->setTaskType($task->getType());
        $transactionLog->setStartTime($this->getTimeProvider()->getCurrentLocalTime());
        $transactionLog->setHasErrors(false);
        $transactionLog->setContext($item->getContext());

        $this->save($transactionLog);

        $task->setTransactionLog($transactionLog);
    }

    public function save(TransactionLog $transactionLog)
    {
        $this->getRepository()->save($transactionLog);
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(TransactionLog $log, $status)
    {
        $log->setStatus($status);
        if ($status === Status::COMPLETED) {
            $log->setCompletedTime($this->getTimeProvider()->getCurrentLocalTime());
            if ($log->hasErrors()) {
                $log->setStatus(Status::PARTIALLY_COMPLETED);
            }
        }

        $this->getRepository()->update($log);
    }

    /**
     * Updates number of synced entities.
     *
     * @param int $logId
     * @param int $syncedEntities
     */
    public function updateSynchronizedEntities($logId, $syncedEntities)
    {
        /** @var TransactionLog $log */
        $log = $this->getById($logId);
        $log->setSynchronizedEntities($syncedEntities);
        $this->getRepository()->update($log);
    }

    /**
     * @inheritDoc
     */
    public function update(TransactionLog $log)
    {
        $this->getRepository()->update($log);
    }

    /**
     * @inheritDoc
     */
    public function load(QueueItem $item)
    {
        /** @var TransactionalTask $task */
        $task = $item->getTask();
        if ($task === null) {
            return;
        }

        $id = $item->getParentId() !== null ? $item->getParentId() : $item->getId();
        $task->setTransactionLog($this->getLatestTransactionLog($id));
    }

    /**
     * @inheritDoc
     */
    public function find(array $query = [], $offset = 0, $limit = 1000)
    {
        $queryFilter = new QueryFilter();
        $queryFilter->setLimit($limit);
        $queryFilter->setOffset($offset);
        $queryFilter->orderBy('id', QueryFilter::ORDER_DESC);

        foreach ($query as $column => $value) {
            if ($value === null) {
                $queryFilter->where($column, Operators::NULL);
            } else if (is_array($value)) {
                $queryFilter->where($column, Operators::IN, $value);
            } else {
                $queryFilter->where($column, Operators::EQUALS, $value);
            }
        }

        return $this->getRepository()->select($queryFilter);
    }

    /**
     * @inheritDoc
     */
    public function count(array $query = [])
    {
        $queryFilter = new QueryFilter();

        foreach ($query as $column => $value) {
            if ($value === null) {
                $queryFilter->where($column, Operators::NULL);
            } else if (is_array($value)) {
                $queryFilter->where($column, Operators::IN, $value);
            } else {
                $queryFilter->where($column, Operators::EQUALS, $value);
            }
        }

        return $this->getRepository()->count($queryFilter);
    }

    private function getLatestTransactionLog($id)
    {
        $filter = new QueryFilter();
        $filter->where('executionId', Operators::EQUALS, $id)
            ->orderBy('id', QueryFilter::ORDER_DESC);

        return $this->getRepository()->selectOne($filter);
    }

    private function getRepository()
    {
        return RepositoryRegistry::getRepository(TransactionLog::getClassName());
    }

    /**
     * @return TimeProvider
     */
    private function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }

    private function getById($logId)
    {
        $filter = new QueryFilter();
        $filter->where('id', Operators::EQUALS, (int)$logId);

        return $this->getRepository()->selectOne($filter);
    }
}