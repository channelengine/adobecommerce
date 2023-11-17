<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Tasks\ReturnSync;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\DTO\Webhook;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;

/**
 * Class ReturnWebhookHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers
 */
class ReturnWebhookHandler extends WebhooksHandler
{
    /**
     * @return void
     *
     * @throws QueueStorageUnavailableException
     */
    protected function doHandle()
    {
        $this->getQueueService()->enqueue(
            'returns-sync',
            new ReturnSync(),
            ConfigurationManager::getInstance()->getContext()
        );
    }

    /**
     * @param Webhook $webhook
     *
     * @return bool
     */
    protected function isWebhookValid(Webhook $webhook)
    {
        return parent::isWebhookValid($webhook) && $webhook->getEvent() === 'returns';
    }
}