<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Config
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Config extends Template
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param UrlHelper $urlHelper
     * @param ProductMetadataInterface $productMetadata
     * @param Context $context
     * @param array $data
     */
    public function __construct(UrlHelper $urlHelper, ProductMetadataInterface $productMetadata, Context $context, array $data = [])
    {
        $this->urlHelper = $urlHelper;
        $this->productMetadata = $productMetadata;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves subpage url.
     *
     * @param string $page
     *
     * @return string
     */
    public function getSubpageUrl(string $page): string
    {
        return $this->urlHelper->getBackendUrl(
            'channelengine/content/state',
            [
                'page' => $page,
                'storeId' => ConfigurationManager::getInstance()->getContext()
            ]
        );
    }

    /**
     * @return bool
     */
    public function isEnterprise(): bool
    {
        return $this->productMetadata->getEdition() === 'Enterprise';
    }

    /**
     * @return string
     */
    public function getConfigUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/configuration/get');
    }

    /**
     * @return string
     */
    public function getDisconnectUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/configuration/disconnect');
    }

    /**
     * @return string
     */
    public function getDisableUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/disable');
    }

    /**
     * @return string
     */
    public function getTriggerSyncUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/triggersync');
    }

    /**
     * @return string
     */
    public function getConfigSaveUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/configuration/save');
    }

    /**
     * @return string
     */
    public function getCheckStatusUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/configuration/checkstatus');
    }

    /**
     * @return string
     */
    public function getStateUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/content/state', ['page' => 'config']);
    }

    /**
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getStoreId(): string
    {
        return $this->getStoreService()->getStoreId();
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }
}
