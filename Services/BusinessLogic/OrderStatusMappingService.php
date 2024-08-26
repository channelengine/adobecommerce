<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\OrderStatusMappings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class OrderStatusMappingService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class OrderStatusMappingService
{

    /**
     * Sets order status mappings configuration.
     *
     * @param OrderStatusMappings $mappings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setOrderStatusMappings(OrderStatusMappings $mappings): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('orderStatusMappings', $mappings->toArray());
    }

    /**
     * Retrieves order status mappings configuration.
     *
     * @return OrderStatusMappings | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getOrderStatusMappings(): ?OrderStatusMappings
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('orderStatusMappings');

        return $rawData ? OrderStatusMappings::fromArray($rawData) : null;
    }
}
