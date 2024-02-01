<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductEventsBufferService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductReplaced;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class ProductReplacedEventHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers
 */
class ProductReplacedEventHandler
{
    /**
     * Handles product replace event.
     *
     * @param ProductReplaced $replaced
     */
    public function handle(ProductReplaced $replaced)
    {
        try {
            if (ConfigurationManager::getInstance()->getConfigValue('syncProducts', 1)) {
                $this->getService()->recordReplaced($replaced);
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