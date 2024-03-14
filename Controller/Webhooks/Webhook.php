<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Webhooks;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\DTO\Webhook as WebhookDTO;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers\OrderWebhookHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Handlers\ReturnWebhookHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ReturnsSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Webhook
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Webhooks
 */
class Webhook implements ActionInterface
{
    use SetsContextTrait;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @param RequestInterface $request
     * @param ProductMetadataInterface $productMetadata
     * @param ResultFactory $resultFactory
     */
    public function __construct(RequestInterface $request,ProductMetadataInterface $productMetadata,
        ResultFactory  $resultFactory)
    {
        $this->productMetadata = $productMetadata;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Handles webhook.
     *
     * @return ResultInterface|void
     *
     * @throws ContextNotSetException
     * @throws QueryFilterInvalidParamException
     */
    public function execute()
    {
        $this->setContext($this->request);

        if (!$this->getPluginStatusService()->isEnabled() || !$this->getStateService()->isOnboardingCompleted()) {
            return;
        }

        $tenant = $this->request->getParam('tenant');
        $token = $this->request->getParam('token');
        $type = $this->request->getParam('type');
        $webhook = new WebhookDTO($tenant, $token, $type);
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $handler = $this->getHandler($type);

        if (!$handler) {
            return;
        }

        try {
            $handler->handle($webhook);
            $response->setHttpResponseCode(200);
        } catch (BaseException $e) {
            Logger::logError($e->getMessage());

            $response->setHttpResponseCode(400);
        }

        return $response;
    }

    /**
     * @param string $type
     *
     * @return OrderWebhookHandler|ReturnWebhookHandler|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getHandler(string $type)
    {
        if ($type === 'orders') {
            return new OrderWebhookHandler();
        }

        if ($type === 'returns' && $this->productMetadata->getEdition() === 'Enterprise' && $this->getReturnsService()->getReturnsSettings()->isReturnsEnabled()) {
            return new ReturnWebhookHandler();
        }

        return null;
    }

    /**
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
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

    /**
     * @return ReturnsSettingsService
     */
    private function getReturnsService(): ReturnsSettingsService
    {
        return ServiceRegister::getService(ReturnsSettingsService::class);
    }
}
