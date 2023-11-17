<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappingsTypes;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class AttributeMappingsTypesService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class AttributeMappingsTypesService
{
    /**
     * Sets attribute mappings types configuration.
     *
     * @param AttributeMappingsTypes $mappings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAttributeMappings(AttributeMappingsTypes $mappings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('attributeMappingsTypes', $mappings->toArray());
    }

    /**
     * Retrieves attribute mappings types configuration.
     *
     * @return AttributeMappingsTypes | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAttributeMappings(): ?AttributeMappingsTypes
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('attributeMappingsTypes');

        return  $rawData ? AttributeMappingsTypes::fromArray($rawData) : null;
    }
}
