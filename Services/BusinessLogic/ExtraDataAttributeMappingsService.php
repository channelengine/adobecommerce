<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\ExtraDataAttributeMappings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class ExtraDataAttributeMappingsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ExtraDataAttributeMappingsService
{
    /**
     * Sets extra data attribute mappings configuration.
     *
     * @param ExtraDataAttributeMappings $mappings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setExtraDataAttributeMappings(ExtraDataAttributeMappings $mappings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('extraDataAttributeMappings', $mappings->toArray());
    }

    /**
     * Retrieves extra data attribute mappings configuration.
     *
     * @return ExtraDataAttributeMappings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getExtraDataAttributeMappings(): ?ExtraDataAttributeMappings
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('extraDataAttributeMappings');

        return $rawData !== null ? ExtraDataAttributeMappings::fromArray($rawData) : $rawData;
    }
}
