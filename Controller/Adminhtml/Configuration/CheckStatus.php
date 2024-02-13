<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\InitialSyncStateService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class CheckStatus
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration
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
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context     $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Checks if sync is in progress.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);

        return $this->resultJsonFactory->create()->setData(['in_progress' => $this->syncInProgress($this->_request)]);
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function syncInProgress(RequestInterface $request): bool
    {
        $statuses = [QueueItem::COMPLETED, QueueItem::FAILED, QueueItem::ABORTED];
        $productSync = $this->getQueueService()->findLatestByType('ProductSync', $request->getParam('storeId'));
        $orderSync = $this->getQueueService()->findLatestByType('OrderSync', $request->getParam('storeId'));

        $isSyncInProgress = $productSync && $orderSync && (!in_array($productSync->getStatus(), $statuses, true)
            || !in_array($orderSync->getStatus(), $statuses, true));

        if (!$isSyncInProgress && $this->getInitialSyncStateService()->checkInitialSyncState(InitialSyncStateService::STARTED)) {
            $this->getInitialSyncStateService()->setInitialSyncState(InitialSyncStateService::FINISHED);
        }

        return $isSyncInProgress;
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

    /**
     * @return InitialSyncStateService
     */
    private function getInitialSyncStateService(): InitialSyncStateService
    {
        return ServiceRegister::getService(InitialSyncStateService::class);
    }
}
