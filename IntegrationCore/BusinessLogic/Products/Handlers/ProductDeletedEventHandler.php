<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductEventsBufferService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductDeleted;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class ProductDeletedEventHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers
 */
class ProductDeletedEventHandler
{
    /**
     * Handles product deleted event.
     *
     * @param ProductDeleted $deleted
     */
    public function handle(ProductDeleted $deleted)
    {
        try {
            if (ConfigurationManager::getInstance()->getConfigValue('syncProducts', 1)) {
                $this->getService()->recordDeleted($deleted);
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