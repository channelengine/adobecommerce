<?php

/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsSyncConfigService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\SyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class ProductsSyncConfigService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products
 */
class ProductsSyncConfigService implements BaseService
{
    /**
     * Sets stock settings configuration.
     *
     * @param SyncConfig $config
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function set(SyncConfig $config)
    {
        ConfigurationManager::getInstance()->saveConfigValue('syncConfig', $config->toArray());
    }

    /**
     * Retrieves stock settings configuration.
     *
     * @return SyncConfig | null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function get()
    {
        $rawData = ConfigurationManager::getInstance()->getConfigValue('syncConfig');

        return $rawData ? SyncConfig::fromArray($rawData) : null;
    }
}