<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\AutoTest;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\LogData;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Singleton;

/**
 * Class AutoTestLogger.
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\AutoConfiguration
 */
class AutoTestLogger extends Singleton implements ShopLoggerAdapter
{
    /**
     * Singleton instance of this class.
     *
     * @var static
     */
    protected static $instance;

    /**
     * Logs a message in system.
     *
     * @param LogData $data Data to log.
     *
     * @throws RepositoryNotRegisteredException
     */
    public function logMessage(LogData $data)
    {
        $repo = RepositoryRegistry::getRepository(LogData::CLASS_NAME);
        $repo->save($data);
    }

    /**
     * Gets all log entities.
     *
     * @return LogData[] An array of the LogData entities, if any.
     *
     * @throws RepositoryNotRegisteredException
     */
    public function getLogs()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return RepositoryRegistry::getRepository(LogData::CLASS_NAME)->select();
    }

    /**
     * Transforms logs to the plain array.
     *
     * @return array An array of logs.
     *
     * @throws RepositoryNotRegisteredException
     */
    public function getLogsArray()
    {
        $result = array();
        foreach ($this->getLogs() as $log) {
            $result[] = $log->toArray();
        }

        return $result;
    }
}
