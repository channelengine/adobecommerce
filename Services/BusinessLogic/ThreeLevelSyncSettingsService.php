<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class StockSettingsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ThreeLevelSyncSettingsService
{
    /**
     * Sets stock settings configuration.
     *
     * @param ThreeLevelSyncSettings $settings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setThreeLevelSyncSettings(ThreeLevelSyncSettings $settings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('threeLevelSyncSettings', $settings->toArray());
    }

    /**
     * Retrieves stock settings configuration.
     *
     * @return ThreeLevelSyncSettings | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getThreeLevelSyncSettings(): ?ThreeLevelSyncSettings
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('threeLevelSyncSettings');

        return $rawData ? ThreeLevelSyncSettings::fromArray($rawData) : null;
    }
}
