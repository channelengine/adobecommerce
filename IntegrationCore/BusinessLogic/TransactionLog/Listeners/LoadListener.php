<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events\BaseQueueItemEvent;

/**
 * Class LoadListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners
 */
class LoadListener extends Listener
{
    /**
     * @inheritDoc
     */
    protected function doHandle(BaseQueueItemEvent $event)
    {
        $this->getService()->load($event->getQueueItem());
    }
}