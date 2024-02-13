<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\ReturnsSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class ReturnsSettingsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ReturnsSettingsService
{
    /**
     * Saves returns settings.
     *
     * @param ReturnsSettings $settings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setReturnsSettings(ReturnsSettings $settings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('returnsSettings', $settings->toArray());
    }

    /**
     * Retrieves returns settings.
     *
     * @return ReturnsSettings | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getReturnsSettings(): ?ReturnsSettings
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('returnsSettings');

        return $rawData ? ReturnsSettings::fromArray($rawData) : null;
    }
}
