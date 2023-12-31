<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces;

/**
 * Interface TaskRunnerWakeup.
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces
 */
interface TaskRunnerWakeup
{
    /**
     * Fully qualified name of this interface.
     */
    const CLASS_NAME = __CLASS__;

    /**
     * Wakes up TaskRunner instance asynchronously if active instance is not already running.
     */
    public function wakeup();
}
