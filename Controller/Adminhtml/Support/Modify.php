<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Support;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\SupportService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole\Contracts\SupportService as SupportServiceInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Modify
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Support
 */
class Modify extends Action
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

    /**
     * Modifies configuration data.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    public function execute()
    {
        $params = json_decode($this->_request->getContent(), true);
        return $this->resultJsonFactory->create()->setData($this->getSupportService()->update($params));
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
