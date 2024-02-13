<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
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
 * Class Enable
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard
 */
class Enable extends Action
{
    use SetsContextTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

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
     * Enables plugin for current store view.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     * @throws QueryFilterInvalidParamException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $this->getStatusService()->enable();
        $this->getStateService()->setOnboardingCompletedIntegrationDisabled(false);
        $this->getStateService()->setOnboardingCompleted(true);

        return $this->resultJsonFactory->create()->setData(['success' => true]);
    }

    /**
     * @return PluginStatusService
     */
    private function getStatusService(): PluginStatusService
    {
        return ServiceRegister::getService(PluginStatusService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }
}
