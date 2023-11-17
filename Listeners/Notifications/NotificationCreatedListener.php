<?php

namespace ChannelEngine\ChannelEngineIntegration\Listeners\Notifications;

use ChannelEngine\ChannelEngineIntegration\Events\NotificationCreatedEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Magento\Framework\Notification\NotifierPool;

/**
 * Class NotificationCreatedListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\Listeners\Notifications
 */
class NotificationCreatedListener
{
    /**
     * Adds Magento admin notification.
     *
     * @param NotificationCreatedEvent $event
     *
     * @return void
     */
    public static function handle(NotificationCreatedEvent $event): void
    {
        $notification = $event->getNotification();

        static::getNotifierPool()->addNotice(
            __('ChannelEngine'),
            sprintf($notification->getMessage(), $notification->getArguments())
        );
    }

    /**
     * @return NotifierPool
     */
    private static function getNotifierPool(): NotifierPool
    {
        return ServiceRegister::getService(NotifierPool::class);
    }
}
