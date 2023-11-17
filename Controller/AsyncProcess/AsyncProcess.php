<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\AsyncProcess;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\AsyncProcessService;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class AsyncProcess
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\AsyncProcess
 */
class AsyncProcess extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var AsyncProcessService
     */
    private $asyncProcessService;

    /**
     * AsyncProcess constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute action based on request and return result.
     */
    public function execute()
    {
        $guid = $this->_request->getParam('guid');
        Logger::logInfo('Received async process request.', 'Integration', ['guid' => $guid]);

        $this->getAsyncProcessService()->runProcess($guid);

        return $this->resultJsonFactory->create(['success' => true]);
    }

    /**
     * @return AsyncProcessService
     */
    private function getAsyncProcessService(): AsyncProcessService
    {
        if ($this->asyncProcessService === null) {
            $this->asyncProcessService = ServiceRegister::getService(AsyncProcessService::CLASS_NAME);
        }

        return $this->asyncProcessService;
    }
}
