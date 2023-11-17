<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\DTO\SystemInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;

/**
 * Class ConfigService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration
 */
abstract class ConfigService extends Configuration
{
    /**
     * Max task inactivity period is set to 15 minutes
     * since Channel Engine rate limit is 10000 requests in 15 minutes.
     * If http call fails due to reached rate limit,
     * it will be performed again when rate limit is reset.
     */
    const MAX_TASK_INACTIVITY_PERIOD = 900;
    /**
     * Default sync batch size.
     */
    const DEFAULT_SYNC_BATCH_SIZE = 150;
    /**
     * Period of time in which product events should be aggregated.
     */
    const EVENTS_READ_TIME_INTERVAL = 300;

    /**
     * Retrieves sync batch size.
     *
     * @return int
     */
    public function getSynchronizationBatchSize()
    {
        return (int)$this->getConfigValue('syncBatchSize', static::DEFAULT_SYNC_BATCH_SIZE);
    }

    /**
     * Gets max inactivity period for a task in seconds.
     * After inactivity period is passed,
     * system will fail such tasks as expired.
     *
     * @return int Max task inactivity period in seconds if set; otherwise, default MAX_TASK_INACTIVITY_PERIOD.
     */
    public function getMaxTaskInactivityPeriod()
    {
        return $this->getConfigValue('maxTaskInactivityPeriod', self::MAX_TASK_INACTIVITY_PERIOD);
    }

    /**
     * Retrieves last events read time.
     *
     * @return int
     */
    public function getLastEventsReadTime()
    {
	    return $this->getConfigValue('lastEventsReadTime', 0);
    }

    /**
     * Sets last events read time.
     *
     * @param int $lastReadTime
     */
    public function setLastEventsReadTime($lastReadTime)
    {
        $this->saveConfigValue('lastEventsReadTime', $lastReadTime);
    }

    /**
     * Retrieves events time interval config value.
     *
     * @return int
     */
    public function getEventsTimeInterval()
    {
        return $this->getConfigValue('eventsTimeInterval', self::EVENTS_READ_TIME_INTERVAL);
    }

    /**
     * Sets events time interval config value.
     *
     * @param int $interval
     */
    public function setEventsTimeInterval($interval)
    {
        $this->saveConfigValue('eventsTimeInterval', $interval);
    }

    /**
     * Provides last system cleanup timestamp.
     */
    public function getLastSystemCleanupTime()
    {
        return (int)$this->getConfigValue('lastSystemCleanupTime', 0);
    }

    /**
     * Sets last system cleanup time.
     *
     * @param int $time
     */
    public function setLastSystemCleanupTime($time)
    {
        $this->saveConfigValue('lastSystemCleanupTime', (int)$time);
    }

    /**
     * Provides information about the system.
     *
     * @return SystemInfo
     */
    abstract public function getSystemInfo();
}