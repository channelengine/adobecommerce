<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized\AuthorizedProxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\DTO\Webhook;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class Proxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks
 */
class Proxy extends AuthorizedProxy
{
	/**
	 * Creates a webhook.
	 *
	 * @param Webhook $webhook
	 *
	 * @throws RequestNotSuccessfulException
	 * @throws HttpCommunicationException
	 * @throws HttpRequestException
	 * @throws QueryFilterInvalidParamException
	 */
    public function create(Webhook $webhook)
    {
        $request = new HttpRequest('webhooks', $webhook->toArray());
        $this->post($request);
    }

	/**
	 * Deletes webhook by name.
	 *
	 * @param $name
	 *
	 * @throws RequestNotSuccessfulException
	 * @throws HttpCommunicationException
	 * @throws HttpRequestException
	 * @throws QueryFilterInvalidParamException
	 */
    public function deleteWebhook($name)
    {
        $request = new HttpRequest("webhooks/$name");
        $this->delete($request);
    }
}