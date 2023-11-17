<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductEventsBufferService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductEventHandlerTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use DateTime;

/**
 * Class TickEventListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Listeners
 */
class TickEventListener
{
    /**
     * Listens to tick event.
     *
     * @throws QueueStorageUnavailableException
     */
    public static function handle()
    {
        /** @var ProductEventsBufferService $productEventBufferService */
        $productEventBufferService = ServiceRegister::getService(ProductEventsBufferService::class);
        /** @var Configuration $configService */
        $configService = ServiceRegister::getService(Configuration::class);

        $lastReadTime = (new DateTime())->setTimestamp($productEventBufferService->getLastReadTime());
        $interval = $configService->getEventsTimeInterval();
        $nextSyncTime = $lastReadTime->modify("+ $interval seconds");
        $now = new DateTime();

        if ($nextSyncTime <= $now) {
            static::enqueueHandler();
            $productEventBufferService->updateLastReadTime((new DateTime())->getTimestamp());
        }
    }

    protected static function enqueueHandler()
    {
        static::getQueueService()->enqueue('channel-engine-product-events', new ProductEventHandlerTask());
    }

    /**
     * @return QueueService
     */
    protected static function getQueueService()
    {
        return ServiceRegister::getService(QueueService::class);
    }
}