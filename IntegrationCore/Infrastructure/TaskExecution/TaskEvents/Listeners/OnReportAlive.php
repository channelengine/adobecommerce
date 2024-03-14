<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\TaskEvents\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use RuntimeException;

/**
 * Class OnReportAlive
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\TaskEvents\Listeners
 */
class OnReportAlive
{
    /**
     * Handles report alive event.
     *
     * @param QueueItem $queueItem
     *
     * @throws QueueStorageUnavailableException
     */
    public static function handle(QueueItem $queueItem)
    {
        $queue = self::getQueue();
        $queue->keepAlive($queueItem);
        if ($queueItem->getParentId() === null) {
            return;
        }

        $parent = $queue->find($queueItem->getParentId());

        if ($parent === null) {
            throw new RuntimeException("Parent not available.");
        }

        $queue->keepAlive($parent);
    }

    /**
     * Provides queue service.
     *
     * @return QueueService
     */
    private static function getQueue()
    {
        return ServiceRegister::getService(QueueService::CLASS_NAME);
    }
}