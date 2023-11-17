<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Order;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\OrdersPage;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\Context;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Traits\NotificationCreator;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\OrdersChannelSupportCache;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Contracts\OrdersService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Domain\CreateResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class OrdersDownloadTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Tasks
 */
class OrdersDownloadTask extends TransactionalTask
{
    use NotificationCreator;

    const CLASS_NAME = __CLASS__;

    /**
     * @const string
     */
    const MERCHANT_TYPE_OF_FULFILLMENT = 'Merchant';

    /**
     * @var int
     */
    protected $successNumber = 0;

    /**
     * @var int
     */
    protected $failedNumber = 0;

    /**
     * @var int
     */
    protected $totalSyncedOrders = 0;
    /**
     * @var array
     */
    protected $failedOrdersList = [];

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return Serializer::serialize($this->toArray());
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $unserialized = Serializer::unserialize($serialized);
        $this->successNumber = $unserialized['successNumber'];
        $this->failedNumber = $unserialized['failedNumber'];
        $this->totalSyncedOrders = $unserialized['totalSyncedOrders'];
        $this->failedOrdersList = $unserialized['failedOrdersList'];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        $task = new self();
        $task->successNumber = $array['successNumber'];
        $task->failedNumber = $array['failedNumber'];
        $task->totalSyncedOrders = $array['totalSyncedOrders'];
        $task->failedOrdersList = $array['failedOrdersList'];

        return $task;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'successNumber' => $this->successNumber,
            'failedNumber' => $this->failedNumber,
            'totalSyncedOrders' => $this->totalSyncedOrders,
            'failedOrdersList' => $this->failedOrdersList,
        ];
    }

    /**
     * Downloads orders fulfilled by the merchant.
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     * @throws HttpRequestException
     */
    public function execute()
    {
        $executionSyncedOrders = 0;

        while (true) {
            $this->reportAlive();

            $ordersPage = $this->getOrdersPage();
            $totalCount = $ordersPage->getTotalCount();
            $orders = $ordersPage->getOrders();
            $orders = $this->removeFailedOrders($orders);

            if (empty($orders)) {
                break;
            }

            foreach ($orders as $index => $order) {
                $order->setTypeOfFulfillment(self::MERCHANT_TYPE_OF_FULFILLMENT);

                $createResponse = $this->getOrdersService()->create($order);

                if ($createResponse->getSuccess()) {
                    $this->acknowledgeOrder($order, $createResponse);
                } else {
                    $this->markFailed($order, $createResponse);
                }

                $executionSyncedOrders++;

                if (($index % 10) === 0) {
                    $this->getTransactionLog()->setSynchronizedEntities($this->totalSyncedOrders);
                    $this->reportProgress($this->getCurrentProgress($totalCount ?: 1, $executionSyncedOrders));
                }
            }

            $this->totalSyncedOrders += $executionSyncedOrders;
        }

        $this->getTransactionLog()->setSynchronizedEntities($this->totalSyncedOrders);

        if ($this->failedNumber) {
            $this->addTaskSummary(
                $this->getTransactionLog(),
                'Order download finished with errors.',
                $this->totalSyncedOrders === 0 ? Context::ERROR : Context::WARNING
            );
        }

        $this->reportProgress(100);
    }

    /**
     * Retrieves new orders page.
     *
     * @return OrdersPage
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    protected function getOrdersPage()
    {
        return $this->getOrderProxy()->getNew();
    }

    /**
     * Acknowledges successfully synchronized order.
     *
     * @param Order $order
     * @param CreateResponse $createResponse
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    protected function acknowledgeOrder(Order $order, CreateResponse $createResponse)
    {
        $this->getOrderChannelSupportCache()->create(
            $createResponse->getShopOrderId(),
            $order->getChannelOrderSupport()
        );
        $acknowledgeResponse = $this->getOrderProxy()->acknowledge(
            $order->getId(),
            $createResponse->getShopOrderId()
        );

        if ($acknowledgeResponse->isSuccess()) {
            $this->successNumber++;
        } else {
            $this->markFailed($order, $acknowledgeResponse);
        }
    }

    /**
     * Marks order as failed.
     *
     * @param Order $order
     * @param $response
     */
    protected function markFailed(Order $order, $response)
    {
        $this->failedNumber++;
        $this->addLogDetail($order, $response);
        $this->transactionLog->setHasErrors(true);
        $this->updateFailedOrdersList($order->getId());
    }

    /**
     * Removes failed orders from orders array.
     *
     * @param Order[] $orders
     *
     * @return Order[]
     */
    protected function removeFailedOrders(array &$orders)
    {
        foreach ($orders as $key => $order) {
            if (in_array($order->getId(), $this->failedOrdersList, true)) {
                unset($orders[$key]);
            }
        }

        return $orders;
    }

    /**
     * Retrieves current progress.
     *
     * @param int $totalCount
     * @param int $numberOfSynchronizedOrders
     *
     * @return float|int
     */
    protected function getCurrentProgress($totalCount, $numberOfSynchronizedOrders)
    {
        return ($numberOfSynchronizedOrders * 95.0) / $totalCount;
    }

    /**
     * Adds log details.
     *
     * @param Order $order
     * @param $response
     */
    protected function addLogDetail(Order $order, $response)
    {
        $this->getDetailsService()->create(
            $this->getTransactionLog(),
            'Failed to create order with id %s in shop because: %s',
            [$order->getId(), $response->getMessage()]
        );
    }

    /**
     * Updates failed orders list.
     *
     * @param $orderId
     */
    protected function updateFailedOrdersList($orderId)
    {
        $this->failedOrdersList[] = $orderId;
    }

    /**
     * Retrieves instance of order Proxy.
     *
     * @return Proxy
     */
    protected function getOrderProxy()
    {
        return ServiceRegister::getService(Proxy::CLASS_NAME);
    }

    /**
     * Retrieves instance of OrdersService.
     *
     * @return OrdersService
     */
    protected function getOrdersService()
    {
        return ServiceRegister::getService(OrdersService::CLASS_NAME);
    }

    /**
     * Retrieves instance of OrdersChannelSupportCache.
     *
     * @return OrdersChannelSupportCache
     */
    protected function getOrderChannelSupportCache()
    {
        return ServiceRegister::getService(OrdersChannelSupportCache::CLASS_NAME);
    }

    /**
     * @return DetailsService
     */
    protected function getDetailsService()
    {
        return ServiceRegister::getService(DetailsService::class);
    }
}