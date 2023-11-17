<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogAware as ITransactionalLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Traits\TransactionLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\CompositeTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task;


abstract class TransactionalComposite extends CompositeTask implements ITransactionalLogAware
{
    use TransactionLogAware;

    protected function executeSubTask(Task $activeTask)
    {
        if ($activeTask instanceof ITransactionalLogAware) {
            $activeTask->setTransactionLog($this->getTransactionLog());
        }

        parent::executeSubTask($activeTask);
    }

    /**
     * @inheritDoc
     */
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