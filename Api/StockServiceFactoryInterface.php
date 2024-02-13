<?php

namespace ChannelEngine\ChannelEngineIntegration\Api;

use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\StockServiceInterface;

/**
 * Interface StockServiceFactoryInterface
 *
 * @package ChannelEngine\ChannelEngineIntegration\Api
 */
interface StockServiceFactoryInterface
{
    /**
     * Creates an instance of StockService based on whether MSI is enabled or disabled.
     *
     * @return StockServiceInterface
     */
    public function create(): StockServiceInterface;
}
