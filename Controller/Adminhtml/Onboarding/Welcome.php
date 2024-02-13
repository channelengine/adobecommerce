<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Welcome
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding
 */
class Welcome extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Welcome controller constructor.
     *
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
     * Sets onboarding started flag.
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        try {
            $this->getStateService()->setOnboardingStarted(true);
            return $this->resultJsonFactory->create()->setData(['status' => true]);
        } catch (QueryFilterInvalidParamException $e) {
            Logger::logError($e->getMessage());
            return $this->resultJsonFactory->create()->setData(['status' => false]);
        }
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }
}
