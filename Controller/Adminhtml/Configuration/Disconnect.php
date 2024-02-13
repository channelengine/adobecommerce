<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\DisconnectService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Disconnect
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration
 */
class Disconnect extends Action
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
    public function __construct(
        Context     $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Disconnects current store from ChannelEngine.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $this->getDisconnectService()->disconnect();

        return $this->resultJsonFactory->create()->setData(['success' => true]);
    }

    /**
     * @return DisconnectService
     */
    private function getDisconnectService(): DisconnectService
    {
        return ServiceRegister::getService(DisconnectService::class);
    }
}
