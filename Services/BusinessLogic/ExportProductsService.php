<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class ExportProductsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ExportProductsService
{
    /**
     * Checks if export products is enabled.
     *
     * @return bool
     */
    public function isExportProductsEnabled(): bool
    {
        try {
            return ConfigurationManager::getInstance()->getConfigValue('syncProducts', 1);
        } catch (QueryFilterInvalidParamException $exception) {
            Logger::logError($exception->getMessage());

            return false;
        }
    }

    /**
     * Enables products export.
     *
     * @return void
     * @throws QueryFilterInvalidParamException
     */
    public function enableProductsExport(): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('syncProducts', 1);
    }

    /**
     * Disables products export.
     *
     * @return void
     * @throws QueryFilterInvalidParamException
     */
    public function disableProductsExport(): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('syncProducts', 0);
    }
}
