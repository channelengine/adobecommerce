<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\AsyncProcess;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\AsyncProcessService;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;


/**
 * Class AsyncProcess
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\AsyncProcess
 */
class AsyncProcess implements HttpGetActionInterface, HttpPostActionInterface
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
     * @var RequestInterface
     */
    private $request;

    /**
     * AsyncProcess constructor.
     *
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(RequestInterface $request,JsonFactory $resultJsonFactory)
    {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute action based on request and return result.
     */
    public function execute()
    {
        $guid = $this->request->getParam('guid');
        Logger::logInfo('Received async process request.', 'Integration', ['guid' => $guid]);

        $this->getAsyncProcessService()->runProcess($guid);
        $result = $this->resultJsonFactory->create();
        $result->setData(['success' => true]);
        return $result;
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
