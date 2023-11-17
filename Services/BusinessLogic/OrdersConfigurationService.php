<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService as CoreOrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class OrdersConfigurationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class OrdersConfigurationService extends CoreOrdersConfigurationService
{
    /**
     * @inheirtDoc
     * @throws QueryFilterInvalidParamException
     */
    public function saveOrderSyncConfig(OrderSyncConfig $syncConfig): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('ordersSyncConfig', $syncConfig->toArray());
    }

    /**
     * @throws QueryFilterInvalidParamException
     */
    public function getOrderSyncConfig(): ?OrderSyncConfig
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('ordersSyncConfig');

        return $rawData ? OrderSyncConfig::fromArray($rawData) : null;
    }
}
