<?php

namespace ChannelEngine\ChannelEngineIntegration\Listeners\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\Tasks\ProductEventHandlerTask;

/**
 * Class TickEventListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\Listeners\Products
 */
class TickEventListener extends \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Listeners\TickEventListener
{
    /**
     * Enqueues ProductEventHandlerTask.
     *
     * @return void
     *
     * @throws QueueStorageUnavailableException
     */
    protected static function enqueueHandler(): void
    {
        static::getQueueService()->enqueue('channel-engine-product-events', new ProductEventHandlerTask());
    }
}
