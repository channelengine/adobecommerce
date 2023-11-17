<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;

interface TransactionLogAware
{
    /**
     * Provides transaction log.
     *
     * @return TransactionLog
     */
    public function getTransactionLog();

    /**
     * Sets transaction log.
     *
     * @param TransactionLog $transactionLog
     */
    public function setTransactionLog($transactionLog);
}