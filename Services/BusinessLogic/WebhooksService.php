<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\Enums\EventTypes;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\WebhooksService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class WebhooksService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class WebhooksService extends BaseService
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param UrlHelper $urlHelper
     */
    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Creates webhook unique id.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function createWebhookUniqueId(): void
    {
        $id = substr($this->getGuidProvider()->generateGuid(), 0, 8);

        ConfigurationManager::getInstance()->saveConfigValue('CHANNELENGINE_WEBHOOK_ID', $id);
    }

    /**
     * Retrieves webhook unique id.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getWebhookUniqueId(): string
    {
        return ConfigurationManager::getInstance()->getConfigValue('CHANNELENGINE_WEBHOOK_ID', '');
    }

    /**
     * @inheritDoc
     */
    protected function getEvents(): array
    {
        return [
            EventTypes::ORDERS_CREATE,
            EventTypes::RETURNS_CHANGE,
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function getName(): string
    {
        return 'magento2_orders_' . $this->getWebhookUniqueId();
    }

    /**
     * @inheritDoc
     *
     * @throws NoSuchEntityException
     */
    protected function getUrl(): string
    {
        return $this->urlHelper->getFrontendUrl(
            'channelengine/webhooks/webhook',
            ['storeId' => ConfigurationManager::getInstance()->getContext()]
        );
    }
}
