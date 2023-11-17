<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\PriceSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class PriceSettingsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class PriceSettingsService
{
    /**
     * Sets price settings.
     *
     * @param PriceSettings $priceSettingsEntity
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setPriceSettings(PriceSettings $priceSettingsEntity): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('priceSettings', $priceSettingsEntity->toArray());
    }

    /**
     * Gets price settings.
     *
     * @return PriceSettings | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getPriceSettings(): ?PriceSettings
    {
        $data = ConfigurationManager::getInstance()->getConfigValue('priceSettings');

        return $data ? PriceSettings::fromArray($data) : null;
    }
}
