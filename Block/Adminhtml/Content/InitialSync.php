<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class InitialSync
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class InitialSync extends Template
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param UrlHelper $urlHelper
     * @param Context $context
     * @param array $data
     */
    public function __construct(UrlHelper $urlHelper, Context $context, array $data = [])
    {
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves initial sync url.
     *
     * @return string
     */
    public function getInitialSyncUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/onboarding/initialsync');
    }

    /**
     * Retrieves state url.
     *
     * @return string
     */
    public function getStateUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/content/state');
    }

    /**
     * Retrieves current store id.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getStoreId(): string
    {
        return $this->getStoreService()->getStoreId();
    }

    /**
     * Retrieves product sync page url.
     *
     * @return string
     */
    public function getProductSyncPageUrl(): string
    {
        return $this->urlHelper->getBackendUrl(
            'channelengine/content/productsync',
            [
                'page' => StateService::PRODUCT_CONFIGURATION,
                'storeId' => ConfigurationManager::getInstance()->getContext(),
            ]
        );
    }

    /**
     * Retrieves order sync page url.
     *
     * @return string
     */
    public function getOrderSyncPageUrl(): string
    {
        return $this->urlHelper->getBackendUrl(
            'channelengine/content/ordersettings',
            [
                'page' => StateService::ORDER_SETTINGS,
                'storeId' => ConfigurationManager::getInstance()->getContext(),
            ]
        );
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }
}
