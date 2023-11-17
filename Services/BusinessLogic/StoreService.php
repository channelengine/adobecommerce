<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class StoreService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class StoreService
{
    /**
     * Sets connected store view id.
     *
     * @param string $id
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setStoreId(string $id): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('connectedStoreView', $id);
    }

    /**
     * Retrieves connected store view id.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getStoreId(): string
    {
        return ConfigurationManager::getInstance()->getConfigValue('connectedStoreView', '');
    }

    /**
     * Retrieves id of first connected store view.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getFirstConnectedStoreId(): string
    {
        return ConfigurationManager::getInstance()->getConfigValue('connectedStoreView', '', false);
    }
}
