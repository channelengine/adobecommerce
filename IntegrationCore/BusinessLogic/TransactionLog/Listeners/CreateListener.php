<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events\BaseQueueItemEvent;

/**
 * Class CreateListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners
 */
class CreateListener extends Listener
{
    /**
     * @inheritDoc
     */
    protected function doHandle(BaseQueueItemEvent $event)
    {
        $queueItem = $event->getQueueItem();

        if ($queueItem->getParentId() !== null) {
            return;
        }

        $this->getService()->create($queueItem);
    }
}