<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\OrderSync;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;

/**
 * Class TickEventHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Handlers
 */
class TickEventHandler
{
    /**
     * @var QueueService
     */
    protected $queueService;

    /**
     * Handles tick event for orders.
     *
     * @throws QueueStorageUnavailableException
     */
    public function handleOrders()
    {
        $this->enqueueTask(new OrderSync());
    }

    /**
     * Enqueues task.
     *
     * @param $task
     *
     * @throws QueueStorageUnavailableException
     */
    protected function enqueueTask($task)
    {
        $this->getQueueService()->enqueue('orders-sync', $task);
    }

    /**
     * @return QueueService
     */
    protected function getQueueService()
    {
        if ($this->queueService === null) {
            $this->queueService = ServiceRegister::getService(QueueService::class);
        }

        return $this->queueService;
    }
}