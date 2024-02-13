<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class Dashboard
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Dashboard extends Template
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
     * Retrieves plugin status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        try {
            return $this->getState();
        } catch (QueryFilterInvalidParamException $e) {
            return 'notifications';
        }
    }

    /**
     * Retrieves enable plugin url.
     *
     * @return string
     */
    public function getEnablePluginUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/enable');
    }

    /**
     * Retrieves disable plugin url.
     *
     * @return string
     */
    public function getDisablePluginUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/disable');
    }

    /**
     * Retrieves trigger sync url.
     *
     * @return string
     */
    public function getTriggerSyncUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/triggersync');
    }

    /**
     * Retrieves check status url.
     *
     * @return string
     */
    public function getCheckStatusUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/checkstatus');
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
     * Checks if integration is disabled due to a deleted three sync level attribute.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function shouldRenderIntegrationDisabledThreeLevelSyncMessage(): bool
    {
        $threeLevelSyncSettings = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
        return $threeLevelSyncSettings && $threeLevelSyncSettings->getAttributeDeleted();
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
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }

    /**
     * Retrieves dashboard state.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function getState(): string
    {
        if (!$this->getPluginStatusService()->isEnabled()) {
            return 'disabled-integration';
        }

        if ($this->getStateService()->isInitialSyncInProgress()) {
            return 'sync-in-progress';
        }

        $manualProductSync = $this->getStateService()->isManualProductSyncInProgress();
        $manualOrderSync = $this->getStateService()->isManualOrderSyncInProgress();

        if ($manualOrderSync && $manualProductSync) {
            return 'sync-in-progress';
        }

        if ($manualOrderSync) {
            return 'order-sync-in-progress';
        }

        if ($manualProductSync) {
            return 'product-sync-in-progress';
        }

        if ($this->getNotificationService()->countNotRead(['context' => ConfigurationManager::getInstance()->getContext()]) > 0) {
            return 'notifications';
        }

        return 'sync-completed';
    }

    /**
     * @return NotificationService
     */
    private function getNotificationService(): NotificationService
    {
        return ServiceRegister::getService(NotificationService::class);
    }

    /**
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
    {
        return new PluginStatusService();
    }

    /**
     * @return ThreeLevelSyncSettingsService
     */
    private function getThreeLevelSyncSettingsService(): ThreeLevelSyncSettingsService
    {
        return ServiceRegister::getService(ThreeLevelSyncSettingsService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }
}
