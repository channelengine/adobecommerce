<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;

interface DetailsService
{
    /**
     * Creates transaction log.
     *
     * @param TransactionLog $log
     * @param $message
     * @param array $arguments
     *
     * @return void
     */
    public function create(TransactionLog $log, $message, $arguments = []);

    /**
     * Fetch transaction log details for given log id.
     *
     * @param $logId
     *
     * @return Details[]
     */
    public function getForLog($logId);

    /**
     * Search details.
     *
     * @param array $query
     * @param int $offset
     * @param int $limit
     *
     * @return Details[]
     */
    public function find(array $query = [], $offset = 0, $limit = 1000);

	/**
	 * Retrieves number of transaction logs.
	 *
	 * @return int
	 */
	public function count(array $query = []);
}