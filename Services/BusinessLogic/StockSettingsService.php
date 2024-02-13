<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\StockSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class StockSettingsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class StockSettingsService
{
    /**
     * Sets stock settings configuration.
     *
     * @param StockSettings $settings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setStockSettings(StockSettings $settings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('stockSettings', $settings->toArray());
    }

    /**
     * Retrieves stock settings configuration.
     *
     * @return StockSettings | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getStockSettings(): ?StockSettings
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('stockSettings');

        return $rawData ? StockSettings::fromArray($rawData) : null;
    }
}
