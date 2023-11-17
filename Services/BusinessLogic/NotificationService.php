<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\Events\NotificationCreatedEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\NotificationService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\Events\EventBus;

/**
 * Class NotificationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class NotificationService extends BaseService
{
    /**
     * @inheritDoc
     */
    public function create(Notification $notification): void
    {
        parent::create($notification);

        $this->getEventBus()->fire(new NotificationCreatedEvent($notification));
    }

    /**
     * @return EventBus
     */
    private function getEventBus(): EventBus
    {
        return ServiceRegister::getService(EventBus::class);
    }
}
