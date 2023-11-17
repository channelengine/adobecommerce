<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Interfaces;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\LogData;

/**
 * Interface LoggerAdapter.
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Interfaces
 */
interface LoggerAdapter
{
    /**
     * Log message in system
     *
     * @param LogData $data
     */
    public function logMessage(LogData $data);
}
