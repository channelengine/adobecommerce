<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\SyncConfig;

interface ProductsSyncConfigService
{
    /**
     * Provides saved config.
     *
     * @return SyncConfig | null
     */
    public function get();

    /**
     * Saves sync config.
     *
     * @param SyncConfig $config
     *
     * @return void
     */
    public function set(SyncConfig $config);
}