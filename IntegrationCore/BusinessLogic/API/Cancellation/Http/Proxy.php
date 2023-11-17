<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\Http;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\DTO\Cancellation;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized\AuthorizedProxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class Proxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\Http
 */
class Proxy extends AuthorizedProxy
{
    /**
     * Submits order cancellation.
     *
     * @param Cancellation $cancellation
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     */
    public function submit(Cancellation $cancellation)
    {
        $request = new HttpRequest('cancellations', $cancellation->toArray());
        $this->post($request);
    }
}