<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AttributeAfterDeleteObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class AttributeAfterDeleteObserver implements ObserverInterface
{

    /**
     * @var Initializer
     */
    private $initializer;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * @param Initializer $initializer
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Initializer $initializer,
        StoreManagerInterface $storeManager
    ) {
        $this->initializer = $initializer;
        $this->storeManager = $storeManager;
    }

    /**
     * @throws QueryFilterInvalidParamException
     * @throws NoSuchEntityException|RepositoryClassException
     */
    public function execute(Observer $observer)
    {
        $this->initializer->init();
        /** @var Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();
        $code = $attribute->getAttributeCode();
        $storeId = $this->storeManager->getStore()->getStoreId();
        ConfigurationManager::getInstance()->setContext($storeId);

        $threeLevelSyncSettings = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
        $threeLevelSyncEnabled = $threeLevelSyncSettings && $threeLevelSyncSettings->getEnableThreeLevelSync();
        $threeLevelSyncAttribute = $threeLevelSyncEnabled ? $threeLevelSyncSettings->getSyncAttribute(): null;
        if ($threeLevelSyncEnabled && $code === $threeLevelSyncAttribute) {
            $this->getPluginStatusService()->disable();

            $settings = new ThreeLevelSyncSettings(false, '', true);
            $this->getThreeLevelSyncSettingsService()->setThreeLevelSyncSettings($settings);
        }
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
