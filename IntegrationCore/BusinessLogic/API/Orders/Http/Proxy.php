<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized\AuthorizedProxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\OrdersPage;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Order;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Responses\AcknowledgeResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use DateTime;

/**
 * Class Proxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http
 */
class Proxy extends AuthorizedProxy
{
    const CLASS_NAME = __CLASS__;

    /**
     * Get new orders.
     *
     * @return OrdersPage
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws HttpRequestException
     */
    public function getNew()
    {
        $request = new HttpRequest('orders/new');
        $response = $this->get($request);
        $response = $response->decodeBodyToArray();

        return OrdersPage::fromArray($response);
    }

    /**
     * Get orders with specified status(es).
     *
     * @param DateTime $fromDate
     * @param int $page
     * @param array $statuses One/multiple of [IN_PROGRESS,SHIPPED,IN_BACKORDER,MANCO,CANCELED,IN_COMBI,CLOSED,NEW,RETURNED,REQUIRES_CORRECTION,AWAITING_PAYMENT].
     *
     * @return OrdersPage
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws HttpRequestException
     */
    public function getWithStatuses(DateTime $fromDate, $page, array $statuses = ['SHIPPED', 'CLOSED'])
    {
        $request = new HttpRequest('orders');
        $request->setQueries([
            'statuses' => $statuses,
            'page' => $page,
            'fromDate' => $fromDate->format(DATE_ATOM),
        ]);
        $response = $this->get($request);
        $response = $response->decodeBodyToArray();

        return OrdersPage::fromArray($response);
    }

    /**
     * Acknowledges order.
     *
     * @param int $orderId
     * @param string $merchantOrderNo
     *
     * @return AcknowledgeResponse
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws HttpRequestException
     */
    public function acknowledge($orderId, $merchantOrderNo)
    {
        $request = new HttpRequest('orders/acknowledge');
        $request->setBody(
            [
                'MerchantOrderNo' => $merchantOrderNo,
                'OrderId' => $orderId,
            ]
        );

        try {
            $response = $this->post($request);
        } catch (RequestNotSuccessfulException $e) {
            return AcknowledgeResponse::fromArray(
                [
                    'StatusCode' => $e->getCode(),
                    'Success' => false,
                    'Message' => $e->getMessage(),
                ]
            );
        }

        $response = $response->decodeBodyToArray();

        return AcknowledgeResponse::fromArray($response);
    }

    /**
     * Fetches orders by merchant order numbers.
     *
     * @param array $orderNumbers
     *
     * @return Order[]
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function getOrdersByMerchantOrderNumbers(array $orderNumbers)
    {
        $request = new HttpRequest('orders');
        $request->setQueries([
           'merchantOrderNos' => $orderNumbers
        ]);

        $response = $this->get($request);
        $response = $response->decodeBodyToArray();

        $ordersPage = OrdersPage::fromArray($response);

        return $ordersPage->getOrders();
    }
}
