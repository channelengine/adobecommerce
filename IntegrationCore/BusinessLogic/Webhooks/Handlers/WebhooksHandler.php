<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\DTO\Webhook;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;

/**
 * Class WebhooksHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers
 */
abstract class WebhooksHandler
{
    /**
     * @var WebhooksService
     */
    protected $webhookService;
    /**
     * @var QueueService
     */
    protected $queueService;
    /**
     * @var OrdersConfigurationService
     */
    protected $ordersConfigService;

    /**
     * Handles webhook.
     *
     * @param Webhook $webhook
     *
     * @throws QueueStorageUnavailableException
     * @throws RepositoryNotRegisteredException
     */
    public function handle(Webhook $webhook)
    {
        if (!$this->isWebhookValid($webhook)) {
            return;
        }

        $this->doHandle();
    }

    /**
     * @return void
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueueStorageUnavailableException
     */
    abstract protected function doHandle();

    /**
     * Checks if webhook is valid.
     *
     * @param Webhook $webhook
     *
     * @return bool
     */
    protected function isWebhookValid(Webhook $webhook)
    {
        return !empty($webhook->getToken())
            && $webhook->getToken() === $this->getWebhookService()->getWebhookToken();
    }

    /**
     * @return WebhooksService
     */
    protected function getWebhookService()
    {
        if ($this->webhookService === null) {
            $this->webhookService = ServiceRegister::getService(WebhooksService::class);
        }

        return $this->webhookService;
    }

    /**
     * @return QueueService
     */
    protected function getQueueService()
    {
        if ($this->queueService === null) {
            $this->queueService = ServiceRegister::getService(QueueService::class);
        }

        return $this->queueService;
    }
}