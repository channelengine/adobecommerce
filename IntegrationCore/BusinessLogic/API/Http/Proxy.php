<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\TooManyRequestsException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpClient;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Exception;

/**
 * Class Proxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http
 */
abstract class Proxy
{
    /**
     * Base ChannelEngine API URL.
     */
    const BASE_API_URL = 'channelengine.net/api/';
    /**
     * Protocol.
     */
    const PROTOCOL = 'https:';
    /**
     * Used API version.
     */
    const API_VERSION = 'v2';
    /**
     * Maximum number of request retries.
     */
    const MAX_RETRIES = 5;
    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Proxy constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Performs GET HTTP request.
     *
     * @param HttpRequest $request
     *
     * @return HttpResponse Get HTTP response.
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    protected function get(HttpRequest $request)
    {
        return $this->call(HttpClient::HTTP_METHOD_GET, $request);
    }

    /**
     * Performs DELETE HTTP request.
     *
     * @param HttpRequest $request
     *
     * @return HttpResponse DELETE HTTP response.
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    protected function delete(HttpRequest $request)
    {
        return $this->call(HttpClient::HTTP_METHOD_DELETE, $request);
    }

    /**
     * Performs POST HTTP request.
     *
     * @param HttpRequest $request
     *
     * @return HttpResponse Response instance.
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    protected function post(HttpRequest $request)
    {
        return $this->call(HttpClient::HTTP_METHOD_POST, $request);
    }

    /**
     * Performs PUT HTTP request.
     *
     * @param HttpRequest $request
     *
     * @return HttpResponse Response instance.
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    protected function put(HttpRequest $request)
    {
        return $this->call(HttpClient::HTTP_METHOD_PUT, $request);
    }

    /**
     * Performs PATCH HTTP request.
     *
     * @param HttpRequest $request
     *
     * @return HttpResponse Response instance.
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    protected function patch(HttpRequest $request)
    {
        return $this->call(HttpClient::HTTP_METHOD_PATCH, $request);
    }

    /**
     * Performs HTTP call.
     *
     * @param string $method Specifies which http method is utilized in call.
     * @param HttpRequest $request
     *
     * @return HttpResponse Response instance.
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     * @throws Exception
     */
    protected function call($method, HttpRequest $request)
    {
        $this->prepareRequest($request);

        $url = $this->getRequestUrl($request);

        $response = $this->httpClient->request(
            $method,
            $url,
            $request->getHeaders(),
            json_encode($request->getBody())
        );

        try {
            $this->validateResponse($response);
        } catch (TooManyRequestsException $e) {
            if ($request->getRetries() < self::MAX_RETRIES) {
                $delay = isset($response->getHeaders()['retry-after']) ? $response->getHeaders()['retry-after'] : 1;

                $response = $this->retryWithDelay($delay, $request, $method);
            } else {
                throw new RequestNotSuccessfulException(
                    'Too many retries, request failed with the following message: ' . $e->getMessage(),
                    $e->getCode()
                );
            }
        }

        return $response;
    }

    /**
     * Prepares request for execution.
     *
     * @param HttpRequest $request
     */
    protected function prepareRequest(HttpRequest $request)
    {
        $request->setHeaders(array_merge($request->getHeaders(), $this->getHeaders()));
    }

    /**
     * Retrieves full request url.
     *
     * @param HttpRequest $request
     *
     * @return string Full request url.
     */
    protected function getRequestUrl(HttpRequest $request)
    {
        $url = self::PROTOCOL . '//' . self::BASE_API_URL
            . self::API_VERSION . '/' . $request->getEndpoint();

        if (!empty($request->getQueries())) {
            $url .= '?' . $this->getQueryString($request);
        }

        return $url;
    }

    /**
     * Retrieves request headers.
     *
     * @return array Complete list of request headers.
     *
     */
    protected function getHeaders()
    {
        return array(
            'accept' => 'Accept: application/json',
            'content' => 'Content-Type: application/json',
        );
    }

    /**
     * Validates HTTP response.
     *
     * @param HttpResponse $response Response object to be validated.
     *
     * @throws HttpRequestException
     * @throws TooManyRequestsException
     */
    protected function validateResponse(HttpResponse $response)
    {
        $message[] = $body = $response->getBody();
        $responseBody = json_decode($body, true);

        if (((!isset($responseBody['Content']['RejectedCount'])
                    || $responseBody['Content']['RejectedCount'] === 0) && $response->isSuccessful())
            && (!isset($responseBody['Content']['ProductMessages']) || count($responseBody['Content']['ProductMessages']) === 0)
        ) {
            return;
        }

        if ($response->getStatus() === 429) {
            throw new TooManyRequestsException();
        }

        $httpCode = $response->getStatus();
        if (is_array($responseBody)) {
            if (isset($responseBody['StatusCode'])) {
                $httpCode = $responseBody['StatusCode'];
            }

            if (isset($responseBody['Message'])) {
                $message['message'] = $responseBody['Message'];
            }

            if (isset($responseBody['ValidationErrors'])) {
                $message['validationErrors'] = $responseBody['ValidationErrors'];
            }

            if (isset($responseBody['Content']['RejectedCount'], $responseBody['Content']['ProductMessages'])
                && $responseBody['Content']['RejectedCount'] > 0) {
                $message['errorMessages'] = $responseBody['Content']['ProductMessages'];
            }

            if (isset($responseBody['Content']['ProductMessages']) && count($responseBody['Content']['ProductMessages']) > 0) {
                $message['warningMessages'] = $responseBody['Content']['ProductMessages'];
            }
        }

        throw new HttpRequestException(json_encode($message), $httpCode);
    }

    /**
     * Performs HTTP call after $delay seconds.
     *
     * @param float $delay
     * @param HttpRequest $request
     * @param string $method
     *
     * @return HttpResponse
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    protected function retryWithDelay($delay, HttpRequest $request, $method)
    {
        if ($delay > 0) {
            sleep($delay);
        }

        $request->setRetries($request->getRetries() + 1);

        return $this->call($method, $request);
    }

    /**
     * Prepares request's queries.
     *
     * @param HttpRequest $request
     *
     * @return string
     */
    protected function getQueryString(HttpRequest $request)
    {
        $queryString = '';
        $queries = $request->getQueries();

        foreach ( $queries as $key => $value ) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $queryString .= http_build_query([$key => $item]) . '&';
                }

                unset($queries[$key]);
            }
        }

        return rtrim($queryString . http_build_query($queries), '&');
    }
}
