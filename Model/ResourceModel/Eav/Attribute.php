<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\Eav;

use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as MagentoAttribute;

class Attribute extends MagentoAttribute
{
    public function delete()
    {
        $attribute = $this->getAttributeCode();

        $storeId = $this->_storeManager->getStore()->getStoreId();
        ConfigurationManager::getInstance()->setContext($storeId);
        $threeLevelSyncEnabled = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings()->getEnableThreeLevelSync();
        $threeLevelSyncAttribute = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings()->getSyncAttribute();

        if ($threeLevelSyncEnabled && $attribute === $threeLevelSyncAttribute) {
            $this->getPluginStatusService()->disable();

            $settings = new ThreeLevelSyncSettings(false, '', true);
            $this->getThreeLevelSyncSettingsService()->setThreeLevelSyncSettings($settings);
        }

        return parent::delete();
    }

    /**
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
    {
        return ServiceRegister::getService(PluginStatusService::class);
    }

    /**
     * @return ThreeLevelSyncSettingsService
     */
    private function getThreeLevelSyncSettingsService(): ThreeLevelSyncSettingsService
    {
        return ServiceRegister::getService(ThreeLevelSyncSettingsService::class);
    }
}