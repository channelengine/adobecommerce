<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\DTO\Webhook;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\GuidProvider;

/**
 * Class WebhooksService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks
 */
abstract class WebhooksService implements BaseService
{
    /**
     * @inheritDoc
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function create()
    {
        try {
            $this->delete();
        } catch (BaseException $e) {
            // Before webhook creation, we try to delete a webhook
            // with the same name on ChannelEngine. If a webhook with the same name does not exist,
            // there is no need for any actions.
        }

        $webhook = new Webhook();
        $webhook->setEvents($this->getEvents());
        $webhook->setName($this->getName());
        $webhook->setUrl($this->getWebhookUrl());
        $webhook->setIsActive(true);

        $this->getProxy()->create($webhook);
    }

    /**
     * @inheritDoc
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     */
    public function delete()
    {
        $this->getProxy()->deleteWebhook($this->getName());
    }

    /**
     * Creates webhook token.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function createWebhookToken()
    {
        $token = $this->getGuidProvider()->generateGuid();

        ConfigurationManager::getInstance()->saveConfigValue('CHANNELENGINE_WEBHOOK_TOKEN', $token);
    }

    /**
     * Retrieves webhook token.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getWebhookToken()
    {
        return ConfigurationManager::getInstance()->getConfigValue('CHANNELENGINE_WEBHOOK_TOKEN', '');
    }

    /**
     * Retrieves webhook url.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function getWebhookUrl()
    {
        $url = $this->getUrl();
        $parsedUrl = parse_url($url);
        $separator = isset($parsedUrl['query']) ? '&' : '?';

        return $url . $separator . http_build_query(['token' => $this->getWebhookToken()]);
    }

    /**
     * Provides list of available events.
     *
     * @return array
     */
    abstract protected function getEvents();

    /**
     * Provides webhook name. This name will be used to identify webhook.
     *
     * @retrun string
     */
    abstract protected function getName();

    /**
     * Webhook handling url.
     *
     * @retrun string
     */
    abstract protected function getUrl();

    /**
     * Provides proxy.
     *
     * @return Proxy
     */
    protected function getProxy()
    {
        return ServiceRegister::getService(Proxy::class);
    }

    /**
     * Provides GuidProvider.
     *
     * @return GuidProvider
     */
    protected function getGuidProvider()
    {
        return ServiceRegister::getService(GuidProvider::class);
    }
}