<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogAware as ITransactionalLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Traits\TransactionLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Composite\Orchestrator;

abstract class TransactionalOrchestrator extends Orchestrator implements ITransactionalLogAware
{
    use TransactionLogAware;

    public function reportProgress($progressPercent)
    {
        parent::reportProgress($progressPercent);
    }

    /**
     * @return TransactionLogService
     */
    private function getService()
    {
        return ServiceRegister::getService(TransactionLogService::class);
    }

    public function getTransactionLog()
    {
        if ($this->transactionLog === null) {
            $logs = $this->getService()->find(['executionId' => $this->getExecutionId()], 0, 1);
            $this->transactionLog = !empty($logs[0]) ? $logs[0] : null;
        }

        return $this->transactionLog;
    }
}