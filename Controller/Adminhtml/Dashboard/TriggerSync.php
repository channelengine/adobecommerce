<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\OrderSync;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\ManualSync\ProductsResyncTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Traits\GetPostParamsTrait;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class TriggerSync
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard
 */
class TriggerSync extends Action
{
    use SetsContextTrait;
    use GetPostParamsTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var QueueService
     */
    private $queueService;
    /**
     * @var StateService
     */
    private $stateService;

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
     * Triggers synchronization.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     * @throws QueryFilterInvalidParamException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $postParams = $this->getPostParams();
        $orderSync = $postParams['order_sync'];
        $productSync = $postParams['product_sync'];

        try {
            if ($orderSync) {
                $this->getQueueService()->enqueue(
                    'channel-engine-orders',
                    new OrderSync(),
                    $this->_request->getParam('storeId')
                );
                $this->getStateService()->setManualOrderSyncInProgress(true);
            }

            if ($productSync) {
                $this->getQueueService()->enqueue(
                    'channel-engine-products',
                    new ProductsResyncTask(),
                    $this->_request->getParam('storeId')
                );
                $this->getStateService()->setManualProductSyncInProgress(true);
            }
        } catch (QueueStorageUnavailableException $e) {
            return $this->resultJsonFactory->create()->setData(
                [
                    'success' => false,
                    'message' => sprintf(__('Failed to start initial sync because %s'), $e->getMessage()),
                ]
            );
        }

        return $this->resultJsonFactory->create()->setData(['success' => true]);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        if ($this->stateService === null) {
            $this->stateService = ServiceRegister::getService(StateService::class);
        }

        return $this->stateService;
    }

    /**
     * @return QueueService
     */
    private function getQueueService(): QueueService
    {
        if ($this->queueService === null) {
            $this->queueService = ServiceRegister::getService(QueueService::class);
        }

        return $this->queueService;
    }
}
