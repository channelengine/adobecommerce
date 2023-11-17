<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Order;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\OrdersPage;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\Context;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Traits\NotificationCreator;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Contracts\OrdersService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;
use Exception;

/**
 * Class MarketplaceOrdersDownloadTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Tasks
 */
class MarketplaceOrdersDownloadTask extends TransactionalTask
{
    use NotificationCreator;

    /**
     * @const string
     */
    const MARKETPLACE_TYPE_OF_FULFILLMENT = 'Marketplace';

    /**
     * @var int
     */
    protected $page = 1;
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
        $this->page = $unserialized['page'];
        $this->successNumber = $unserialized['successNumber'];
        $this->failedNumber = $unserialized['failedNumber'];
        $this->totalSyncedOrders = $unserialized['totalSyncedOrders'];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        $task = new self();
        $task->page = $array['page'];
        $task->successNumber = $array['successNumber'];
        $task->failedNumber = $array['failedNumber'];
        $task->totalSyncedOrders = $array['totalSyncedOrders'];

        return $task;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'page' => $this->page,
            'successNumber' => $this->successNumber,
            'failedNumber' => $this->failedNumber,
            'totalSyncedOrders' => $this->totalSyncedOrders,
        ];
    }

    /**
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     * @throws Exception
     */
    public function execute()
    {
        $executionSyncedOrders = 0;
        $lastSyncTime = $this->getLastSyncTime();

        while (true) {
            $ordersPage = $this->getOrdersPage($lastSyncTime);
            $orders = $ordersPage->getOrders();
            $totalCount = $ordersPage->getTotalCount();

            if (empty($orders)) {
                break;
            }

            foreach ($orders as $index => $order) {
                $executionSyncedOrders++;
                $this->createOrderInShop($order);

                if (($index % 10) === 0) {
                    $this->getTransactionLog()->setSynchronizedEntities($this->totalSyncedOrders);
                    $this->reportProgress($this->getCurrentProgress($totalCount ?: 1, $executionSyncedOrders));
                }
            }

            $this->totalSyncedOrders += $executionSyncedOrders;
            $this->page++;
        }

        $this->setLastSyncTime($this->getTimeProvider()->getCurrentLocalTime());
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
     * Retrieves last sync time.
     *
     * @return DateTime
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function getLastSyncTime()
    {
        $orderSyncConfig = $this->getOrdersConfigurationService()->getOrderSyncConfig();
        $lastSyncTime = $this->getOrdersConfigurationService()->getClosedOrdersSyncTime();

        if ($orderSyncConfig && $orderSyncConfig->getFromDate() !== '' && $lastSyncTime === null) {
            $lastSyncTime = new DateTime($orderSyncConfig->getFromDate());
        }

        return $lastSyncTime ?: (new DateTime())->setTimestamp(0);
    }

    /**
     * Updates closed orders last sync time.
     *
     * @param DateTime $lastSyncTime
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function setLastSyncTime(DateTime $lastSyncTime)
    {
        $this->getOrdersConfigurationService()->setClosedOrdersSyncTime($lastSyncTime);
    }

    /**
     * Retrieves order page.
     *
     * @param $lastSyncTime
     *
     * @return OrdersPage
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    protected function getOrdersPage($lastSyncTime)
    {
        return $this->getOrdersProxy()->getWithStatuses($lastSyncTime, $this->page);
    }

    /**
     * Creates order in shop.
     *
     * @param Order $order
     */
    protected function createOrderInShop(Order $order)
    {
        $order->setTypeOfFulfillment(self::MARKETPLACE_TYPE_OF_FULFILLMENT);

        $createResponse = $this->getOrdersService()->create($order);

        if (!$createResponse->getSuccess()) {
            $this->addLogDetail($order, $createResponse);
            $this->getTransactionLog()->setHasErrors(true);
            $this->failedNumber++;
            return;
        }

        $this->successNumber++;
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
     * Retrieves current progress.
     *
     * @param int $totalCount
     * @param int $numberOfSynchronizedOrders
     *
     * @return float|int
     */
    protected function getCurrentProgress($totalCount, $numberOfSynchronizedOrders)
    {
        return ($numberOfSynchronizedOrders * 100.0) / $totalCount;
    }

    /**
     * Retrieves instance of OrdersConfigurationService.
     *
     * @return OrdersConfigurationService
     */
    protected function getOrdersConfigurationService()
    {
        return ServiceRegister::getService(OrdersConfigurationService::CLASS_NAME);
    }

    /**
     * Retrieves instance of orders Proxy.
     *
     * @return Proxy
     */
    protected function getOrdersProxy()
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
     * Provides time provider.
     *
     * @return TimeProvider
     */
    protected function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::class);
    }

    /**
     * @return DetailsService
     */
    protected function getDetailsService()
    {
        return ServiceRegister::getService(DetailsService::class);
    }
}
