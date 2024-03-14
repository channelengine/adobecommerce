<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Contracts\OrdersService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueItemDeserializationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\InitialSyncStateService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class CheckStatus
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard
 */
class CheckStatus extends Action
{
    use SetsContextTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var QueueService
     */
    private $queueService;
    /**
     * @var ProductsService
     */
    private $productsService;
    /**
     * @var TransactionLogService
     */
    private $transactionLogService;
    /**
     * @var OrdersService
     */
    private $orderService;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Gets synchronization data.
     *
     * @return Json
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws QueueItemDeserializationException
     * @throws RepositoryNotRegisteredException
     * @throws RequestNotSuccessfulException
     * @throws ContextNotSetException
     */
    public function execute(): Json
    {
        $this->setContext($this->_request);

        $data = [
            'product_sync' => $this->getTaskData('ProductsResyncTask'),
            'order_sync' => $this->getTaskData('OrderSync'),
        ];

        if (!$this->getInitialSyncStateService()->checkInitialSyncState(InitialSyncStateService::FINISHED) &&
            $data['product_sync']['progress'] === 100 && $data['order_sync']['progress'] === 100) {
            $this->getInitialSyncStateService()->setInitialSyncState(InitialSyncStateService::FINISHED);
        }

        return $this->resultJsonFactory->create()->setData($data);
    }

    /**
     * Retrieves task data.
     *
     * @param string $taskType
     *
     * @return array
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws QueueItemDeserializationException
     * @throws RepositoryNotRegisteredException
     * @throws RequestNotSuccessfulException
     */
    private function getTaskData(string $taskType): array
    {
        $queueItem = $this->getQueueService()->findLatestByType($taskType);

        if ($taskType === 'ProductsResyncTask' && !$queueItem) {
            $queueItem = $this->getQueueService()->findLatestByType('ProductSync');
        }

        if (!$queueItem) {
            return [
                'status' => 'not created',
            ];
        }

        $log = $this->getTransactionLogService()->find([
            'executionId' => $queueItem->getTask()->getExecutionId()
        ])[0];

        $status = ($log && $log->getSynchronizedEntities()) ? $log->getSynchronizedEntities() : 0;
        $count = 0;

        if ($taskType === 'ProductsResyncTask') {
            $count = $this->getProductsService()->count();
        }

        if ($taskType === 'OrderSync') {
            $count = $log ? $log->getTotalCount() : 0;
        }

        if ($count) {
            $status *= 100 / $count;
        } else {
            $status = 100;
        }

        $progress = (int)$status;
        $synced = ($log && $log->getSynchronizedEntities()) ? $log->getSynchronizedEntities() : 0;

        if ($queueItem->getTask()->getType() === 'ProductsResyncTask') {
            $progress = round($progress / 2);
            $synced = round($synced / 2);
        }

        return [
            'status' => $queueItem->getStatus(),
            'progress' => $progress,
            'synced' => $synced,
            'total' => $count ?? '?',
        ];
    }

    /**
     * Retrieves an instance of QueueService.
     *
     * @return QueueService
     */
    private function getQueueService(): QueueService
    {
        if ($this->queueService === null) {
            $this->queueService = ServiceRegister::getService(QueueService::class);
        }

        return $this->queueService;
    }

    /**
     * Retrieves an instance of ProductsService
     *
     * @return ProductsService
     */
    private function getProductsService(): ProductsService
    {
        if ($this->productsService === null) {
            $this->productsService = ServiceRegister::getService(ProductsService::class);
        }

        return $this->productsService;
    }

    /**
     * Retrieves an instance of TransactionLogService.
     *
     * @return TransactionLogService
     */
    private function getTransactionLogService(): TransactionLogService
    {
        if ($this->transactionLogService === null) {
            $this->transactionLogService = ServiceRegister::getService(TransactionLogService::class);
        }

        return $this->transactionLogService;
    }

    /**
     * Retrieves an instance of OrdersService
     *
     * @return OrdersService
     */
    private function getOrdersService(): OrdersService
    {
        if ($this->orderService === null) {
            $this->orderService = ServiceRegister::getService(OrdersService::class);
        }

        return $this->orderService;
    }

    /**
     * @return InitialSyncStateService
     */
    private function getInitialSyncStateService(): InitialSyncStateService
    {
        return ServiceRegister::getService(InitialSyncStateService::class);
    }
}
