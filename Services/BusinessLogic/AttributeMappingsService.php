<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class AttributeMappingsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class AttributeMappingsService
{
    public const PRODUCT_SKU = 'product_sku';
    public const PRODUCT_ID = 'product_id';

    /**
     * Sets attribute mappings configuration.
     *
     * @param AttributeMappings $mappings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAttributeMappings(AttributeMappings $mappings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('attributeMappings', $mappings->toArray());
    }

    /**
     * Retrieves attribute mappings configuration.
     *
     * @return AttributeMappings | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAttributeMappings(): ?AttributeMappings
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('attributeMappings');

        return  $rawData ? AttributeMappings::fromArray($rawData) : null;
    }
}
