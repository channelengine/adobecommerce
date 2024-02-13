<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\OrderSync;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\ProductSync as ProductSyncTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\InitialSyncStateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class InitialSync
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding
 */
class InitialSync extends Action
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
     * Starts initial sync.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     * @throws QueryFilterInvalidParamException
     */
    public function execute()
    {
        try {
            $this->setContext($this->_request);

            if (!$this->getPluginStatusService()->isEnabled()) {
                $this->getStateService()->setOnboardingCompletedIntegrationDisabled(true);
                return $this->resultJsonFactory->create()->setData(['success' => true]);
            }

            $this->getQueueService()->enqueue(
                'channel-engine-products',
                new ProductSyncTask(),
                ConfigurationManager::getInstance()->getContext()
            );
            $this->getQueueService()->enqueue(
                'channel-engine-orders',
                new OrderSync(),
                ConfigurationManager::getInstance()->getContext()
            );
            $this->getStateService()->setInitialSyncInProgress(true);
            $this->getInitialSyncStateService()->setInitialSyncState(InitialSyncStateService::STARTED);
            $this->getPluginStatusService()->enable();

            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (QueueStorageUnavailableException $e) {
            return $this->resultJsonFactory->create()->setData([
                'success' => false,
                'message' => $this->getTranslationService()->translate('initialSyncFail', [$e->getMessage()])
            ]);
        }
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
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
    {
        return ServiceRegister::getService(PluginStatusService::class);
    }

    /**
     * @return TranslationService
     */
    private function getTranslationService(): TranslationService
    {
        return ServiceRegister::getService(TranslationService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * @return InitialSyncStateService
     */
    private function getInitialSyncStateService(): InitialSyncStateService
    {
        return ServiceRegister::getService(InitialSyncStateService::class);
    }
}
