<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductEventsBufferService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductUpsert;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class ProductUpsertEventHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers
 */
class ProductUpsertEventHandler
{
    /**
     * Handles product upsert event.
     *
     * @param ProductUpsert $upsert
     */
    public function handle(ProductUpsert $upsert)
    {
        try {
            if (ConfigurationManager::getInstance()->getConfigValue('syncProducts', 1)) {
                $this->getService()->recordUpsert($upsert);
            }
        } catch (QueryFilterInvalidParamException $exception) {
            Logger::logError($exception->getMessage());
        }
    }

    /**
     * Provides service.
     *
     * @return ProductEventsBufferService
     */
    protected function getService()
    {
        return ServiceRegister::getService(ProductEventsBufferService::class);
    }
}