<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Handlers\TickEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;

/**
 * Class TickEventListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Listeners
 */
class TickEventListener
{
    /**
     * Minimum interval between two consecutive checks.
     */
    const MIN_INTERVAL = 7200;

    /**
     * Listens to tick event.
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueueStorageUnavailableException
     */
    public static function handle()
    {
        if (!static::canHandle()) {
            return;
        }

        static::doHandle();
    }

    /**
     * Checks if event can be handled.
     * Should be implemented in integration.
     *
     * @return bool
     */
    protected static function canHandle()
    {
        return true;
    }

    /**
     * @throws QueueStorageUnavailableException
     * @throws RepositoryNotRegisteredException
     */
    protected static function doHandle()
    {
        $ordersConfigService = static::getOrdersConfigService();

        $checkTime = $ordersConfigService->getLastOrderSyncCheckTime();
        $nextSyncTime = $checkTime->modify("+ " . static::MIN_INTERVAL . " seconds");
        $now = static::getTimeProvider()->getCurrentLocalTime();

        if ($nextSyncTime <= $now) {
            $handler = new TickEventHandler();
            $handler->handleOrders();
            $ordersConfigService->setLastOrderSyncCheckTime(new DateTime());
        }
    }

    /**
     * Retrieves an instance of OrdersConfigurationService.
     *
     * @return OrdersConfigurationService
     */
    protected static function getOrdersConfigService()
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }

    /**
     * @return TimeProvider
     */
    protected static function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
}