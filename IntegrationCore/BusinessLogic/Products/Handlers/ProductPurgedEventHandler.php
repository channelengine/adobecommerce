<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductEventsBufferService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductPurged;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class ProductPurgedEventHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers
 */
class ProductPurgedEventHandler
{
    /**
     * Handles product purge event.
     *
     * @param ProductPurged $purged
     */
    public function handle(ProductPurged $purged)
    {
        try {
            if (ConfigurationManager::getInstance()->getConfigValue('syncProducts', 1)) {
                $this->getService()->recordPurged($purged);
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