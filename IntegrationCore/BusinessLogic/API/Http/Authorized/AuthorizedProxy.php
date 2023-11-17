<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpClient;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class AuthorizedProxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized
 */
class AuthorizedProxy extends Proxy
{
    const CLASS_NAME = __CLASS__;
    /**
     * @var string
     */
    protected $apiKey;
	/**
	 * @var string
	 */
    protected $accountName;

    /**
     * AuthorizedProxy constructor.
     *
     * @param HttpClient $client
     * @param string $accountName
     * @param string $apiKey
     */
    public function __construct(HttpClient $client, $accountName, $apiKey)
    {
        parent::__construct($client);
        $this->accountName = $accountName;
        $this->apiKey = $apiKey;
    }

    /**
     * Performs HTTP call.
     *
     * @param string $method
     * @param HttpRequest $request
     *
     * @return HttpResponse
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws HttpRequestException
     */
    protected function call($method, HttpRequest $request)
    {
        $request->setQueries(array_merge($request->getQueries(), ['apikey' => $this->apiKey]));

        return parent::call($method, $request);
    }

    protected function getRequestUrl( HttpRequest $request ) {
	    $url = self::PROTOCOL . '//' . $this->accountName . '.' . self::BASE_API_URL
	           . self::API_VERSION . '/' . $request->getEndpoint();

	    if (!empty($request->getQueries())) {
		    $url .= '?' . $this->getQueryString($request);
	    }

	    return $url;
    }
}
