<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogAware as ITransactionalLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Traits\TransactionLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task as BaseTask;

/**
 * Class TransactionalTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks
 */
abstract class TransactionalTask extends BaseTask implements ITransactionalLogAware
{
    use TransactionLogAware;

    public function reportProgress($progressPercent)
    {
        $this->getService()->updateSynchronizedEntities($this->getTransactionLog()->getId(), $this->getTransactionLog()->getSynchronizedEntities());

        parent::reportProgress($progressPercent);
    }

    /**
     * @return TransactionLogService
     */
    private function getService()
    {
        return ServiceRegister::getService(TransactionLogService::class);
    }
}