<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class PluginStatusService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class PluginStatusService
{
    /**
     * Enables integration.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function enable(): void
    {
        $this->setStatus(true);
    }

    /**
     * Disables integration.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function disable(): void
    {
        $this->setStatus(false);
    }

    /**
     * Checks if integration is enabled.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isEnabled(): bool
    {
        return $this->getStatus() === true;
    }

    /**
     * @param $status
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    private function setStatus($status): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('pluginStatus', $status);
    }

    /**
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getStatus(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('pluginStatus', true);
    }
}
