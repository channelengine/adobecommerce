<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Support;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole\Contracts\SupportService as SupportServiceInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole\SupportService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Display
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Support
 */
class Display extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var SupportService
     */
    private $supportService;

    /**
     * CheckStatus constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        return $this->resultJsonFactory->create()->setData($this->getSupportService()->get());
    }

    /**
     * @return SupportService
     */
    private function getSupportService(): SupportService
    {
        if ($this->supportService === null) {
            $this->supportService = ServiceRegister::getService(SupportServiceInterface::class);
        }

        return $this->supportService;
    }
}
