<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\Http;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized\AuthorizedProxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnUpdate;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\ReturnsPage;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use DateTime;

/**
 * Class Proxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\Http
 */
class Proxy extends AuthorizedProxy
{
    /**
     * Mark (part of) an order as returned by the customer.
     *
     * @param MerchantReturnRequest $merchantReturnRequest
     *
     * @return void
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function create(MerchantReturnRequest $merchantReturnRequest)
    {
        $request = new HttpRequest('returns/merchant', $merchantReturnRequest->toArray());

        $this->post($request);
    }

    /**
     * Marks a return as received.
     *
     * @param MerchantReturnUpdate $merchantReturnUpdate
     *
     * @return void
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     */
    public function update(MerchantReturnUpdate $merchantReturnUpdate)
    {
        $request = new HttpRequest('returns', $merchantReturnUpdate->toArray());

        $this->put($request);
    }

    /**
     * Retrieve returns from ChannelEngine.
     *
     * @param DateTime $fromDate
     * @param int $page
     *
     * @return ReturnsPage
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function getReturns(DateTime $fromDate, $page)
    {
        $request = new HttpRequest('returns');
        $request->setQueries(
            [
                'fromDate' => $fromDate->format(DATE_ATOM),
                'page' => $page,
            ]
        );

        $response = $this->get($request);
        $responseBody = $response->decodeBodyToArray();

        return ReturnsPage::fromArray($responseBody);
    }
}