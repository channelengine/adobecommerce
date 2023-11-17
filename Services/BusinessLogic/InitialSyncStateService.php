<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class InitiralSyncStateService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class InitialSyncStateService
{
    public const NONE = 'none';
    public const STARTED = 'started';
    public const FINISHED = 'finished';

    /**
     * @param string $state
     *
     * @return bool
     */
    public function checkInitialSyncState(string $state): bool
    {
        return $this->getConfigService()->checkInitialSyncState($state);
    }

    /**
     * Sets initial sync status.
     *
     * @param string $value
     */
    public function setInitialSyncState(string $value): void
    {
        $this->getConfigService()->setInitialSyncState($value);
    }

    /**
     * Retrieves instance of Configuration.
     *
     * @return Configuration
     */
    protected function getConfigService(): Configuration
    {
        return ServiceRegister::getService(Configuration::class);
    }
}
