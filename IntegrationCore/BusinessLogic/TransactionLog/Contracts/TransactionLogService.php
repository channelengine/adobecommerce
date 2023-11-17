<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;

interface TransactionLogService
{
    /**
     * Creates transaction log. Fails existing.
     *
     * @param QueueItem $item
     *
     * @return void
     */
    public function create(QueueItem $item);

    /**
     * Saves transaction log.
     *
     * @param TransactionLog $transactionLog
     *
     * @return void
     */
    public function save(TransactionLog $transactionLog);

    /**
     * Updates transaction log status.
     *
     * @param TransactionLog $log
     * @param $status
     *
     * @return void
     */
    public function updateStatus(TransactionLog $log, $status);

    /**
     * Updates number of synced entities.
     *
     * @param int $logId
     * @param int $syncedEntities
     */
    public function updateSynchronizedEntities($logId, $syncedEntities);

    /**
     * Loads transaction log.
     *
     * @param QueueItem $item
     *
     * @return void
     */
    public function load(QueueItem $item);

    /**
     * Finds transaction logs.
     *
     * @param array $query
     * @param int $offset
     * @param int $limit
     *
     * @return TransactionLog[]
     */
    public function find(array $query = [], $offset = 0, $limit = 1000);

    /**
     * Updates transaction log.
     *
     * @param TransactionLog $log
     *
     * @return void
     */
    public function update(TransactionLog $log);

	/**
	 * Retrieves number of transaction logs.
	 *
	 * @return int
	 */
    public function count(array $query = []);
}