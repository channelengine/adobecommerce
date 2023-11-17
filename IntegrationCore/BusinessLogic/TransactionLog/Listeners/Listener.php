<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogAware;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events\BaseQueueItemEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueItemDeserializationException;

/**
 * Class Listener
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners
 */
abstract class Listener
{
    /**
     * Manages transaction log on state change.
     *
     * @param BaseQueueItemEvent $event
     *
     * @throws QueueItemDeserializationException
     */
    public function handle(BaseQueueItemEvent $event)
    {
        if (!$this->canHandle($event)) {
            return;
        }

        $this->doHandle($event);
    }

    /**
     * Handles the event.
     *
     * @param BaseQueueItemEvent $event
     */
    abstract protected function doHandle(BaseQueueItemEvent $event);

    /**
     * Check if event should be handled.
     *
     * @param BaseQueueItemEvent $event
     * @return bool
     *
     * @throws QueueItemDeserializationException
     */
    protected function canHandle(BaseQueueItemEvent $event)
    {
        $task = $event->getQueueItem()->getTask();
        if ($task === null) {
            return false;
        }

        return $task instanceof TransactionLogAware;
    }

    /**
     * Provides transaction log service.
     *
     * @return TransactionLogService
     */
    protected function getService()
    {
        return ServiceRegister::getService(TransactionLogService::class);
    }
}