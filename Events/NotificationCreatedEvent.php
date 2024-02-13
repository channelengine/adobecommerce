<?php

namespace ChannelEngine\ChannelEngineIntegration\Events;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\Events\Event;

/**
 * Class NotificationCreatedEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\Events
 */
class NotificationCreatedEvent extends Event
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return Notification
     */
    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
