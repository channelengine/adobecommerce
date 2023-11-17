<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\OrderSync;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\DTO\Webhook;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use DateTime;

/**
 * Class OrderWebhookHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers
 */
class OrderWebhookHandler extends WebhooksHandler
{
    /**
     * @return void
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueueStorageUnavailableException
     */
    protected function doHandle()
    {
        $this->getQueueService()->enqueue(
            'order-sync',
            new OrderSync(),
            ConfigurationManager::getInstance()->getContext()
        );
        $this->getOrdersConfigService()->setLastOrderSyncCheckTime(new DateTime());
    }

    /**
     * @param Webhook $webhook
     *
     * @return bool
     */
    protected function isWebhookValid(Webhook $webhook)
    {
        return parent::isWebhookValid($webhook) && $webhook->getEvent() === 'orders';
    }

    /**
     * @return OrdersConfigurationService
     */
    protected function getOrdersConfigService()
    {
        if ($this->ordersConfigService === null) {
            $this->ordersConfigService = ServiceRegister::getService(OrdersConfigurationService::class);
        }

        return $this->ordersConfigService;
    }
}