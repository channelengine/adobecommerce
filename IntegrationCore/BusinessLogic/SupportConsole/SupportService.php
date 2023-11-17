<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\TaskRunnerWakeup;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Process;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use Exception;

/**
 * Class SupportService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole
 */
abstract class SupportService implements Contracts\SupportService
{
    /**
     * @var Configuration
     */
    protected $configService;
    /**
     * @var AuthorizationService
     */
    protected $authService;

    /**
     * @inheritDoc
     */
    public function get()
    {
        /** @var TaskRunnerWakeup $wake_up_service */
        $wake_up_service = ServiceRegister::getService(TaskRunnerWakeup::CLASS_NAME);
        try {
            $wake_up_service->wakeup();
            $wake_up_status = 'successful';
        } catch (Exception $e) {
            $wake_up_status = "Failed because {$e->getMessage()}";
        }

        try {
            $processes = $this->getProcesses();
        } catch (Exception $e) {
            $processes = "Unavailable because: {$e->getMessage()}";
        }

        try {
            $queued_items = $this->getItems(QueueItem::QUEUED);
        } catch (Exception $e) {
            $queued_items = "Unavailable because: {$e->getMessage()}";
        }

        try {
            $in_progress_items = $this->getItems(QueueItem::IN_PROGRESS);
        } catch (Exception $e) {
            $in_progress_items = "Unavailable because: {$e->getMessage()}";
        }

        try {
            $failed_items = $this->getItems(QueueItem::FAILED);
        } catch (Exception $e) {
            $failed_items = "Unavailable because: {$e->getMessage()}";
        }

        $result = array(
            'CONTEXT' => ConfigurationManager::getInstance()->getContext(),
            'INTEGRATION_NAME' => $this->getConfigService()->getIntegrationName(),
            'MAX_STARTED_TASK_LIMIT' => $this->getConfigService()->getMaxStartedTasksLimit(),
            'MAX_TASK_EXECUTION_RETRIES' => $this->getConfigService()->getMaxTaskExecutionRetries(),
            'MAX_TASK_INACTIVITY_PERIOD' => $this->getConfigService()->getMaxTaskInactivityPeriod(),
            'MAX_ALIVE_TIME' => $this->getConfigService()->getTaskRunnerMaxAliveTime(),
            'ASYNC_STARTER_BATCH_SIZE' => $this->getConfigService()->getAsyncStarterBatchSize(),
            'TASK_RUNNER_STATUS' => $this->getConfigService()->getTaskRunnerStatus(),
            'TASK_RUNNER_WAKEUP_DELAY' => $this->getConfigService()->getTaskRunnerWakeupDelay(),
            'TASK_RUNNER_HALTED' => $this->getConfigService()->isTaskRunnerHalted(),
            'MIN_LOG_LEVEL_USER' => $this->getConfigService()->getMinLogLevel(),
            'QUEUED_COUNT' => is_array($queued_items) ? count($queued_items) : $queued_items,
            'QUEUED_ITEMS' => $queued_items,
            'IN_PROGRESS_COUNT' => is_array($in_progress_items) ? count($in_progress_items) : $in_progress_items,
            'IN_PROGRESS_ITEMS' => $in_progress_items,
            'FAILED_COUNT' => is_array($failed_items) ? count($failed_items) : $failed_items,
            'FAILED_ITEMS' => $failed_items,
            'PUBLIC_AVAILABLE_URLS' => $this->getPublicAvailableUrls(),
            'WAKEUP_STATUS' => $wake_up_status,
            'TASK_EXECUTION_PROCESSES' => $processes,
        );

        try {
            $result['MIN_LOG_LEVEL_GLOBAL'] = $this->getGlobalMinLogLevel();
        } catch (Exception $e) {
            $result['MIN_LOG_LEVEL_GLOBAL'] = "Unavailable because: {$e->getMessage()}";
        }

        try {
            $result['CHANNELENGINE_API_KEY'] = $this->getAuthorizationService()->getAuthInfo()->getApiKey();
        } catch (Exception $e) {
            $result['CHANNELENGINE_API_KEY'] = "Unavailable because: {$e->getMessage()}";
        }

        return $result;
    }

    /**
     * @inheritDoc
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    public function update(array $payload)
    {
        if (array_key_exists('MAX_STARTED_TASK_LIMIT', $payload)) {
            $this->getConfigService()->setMaxStartedTasksLimit((int)$payload['MAX_STARTED_TASK_LIMIT']);
        }

        if (array_key_exists('MIN_LOG_LEVEL_USER', $payload)) {
            $this->getConfigService()->saveMinLogLevel((int)$payload['MIN_LOG_LEVEL_USER']);
        }

        if (array_key_exists('MIN_LOG_LEVEL_GLOBAL', $payload)) {
            $this->saveGlobalMinLogLevel((int)$payload['MIN_LOG_LEVEL_GLOBAL']);
        }

        if (array_key_exists('ASYNC_STARTER_BATCH_SIZE', $payload)) {
            $this->getConfigService()->setAsyncStarterBatchSize((int)$payload['ASYNC_STARTER_BATCH_SIZE']);
        }

        if (array_key_exists('MAX_TASK_INACTIVITY_PERIOD', $payload)) {
            $this->getConfigService()->setMaxTaskInactivityPeriod((int)$payload['MAX_TASK_INACTIVITY_PERIOD']);
        }

        if (array_key_exists('TASK_RUNNER_WAKEUP_DELAY', $payload)) {
            $this->getConfigService()->setTaskRunnerWakeupDelay((int)$payload['TASK_RUNNER_WAKEUP_DELAY']);
        }

        if (array_key_exists('TASK_RUNNER_WAKEUP_DELAY', $payload)) {
            $this->getConfigService()->setTaskRunnerWakeupDelay((int)$payload['TASK_RUNNER_WAKEUP_DELAY']);
        }

        if (array_key_exists('MAX_ALIVE_TIME', $payload)) {
            $this->getConfigService()->setTaskRunnerMaxAliveTime((int)$payload['MAX_ALIVE_TIME']);
        }

        if (array_key_exists('MAX_TASK_EXECUTION_RETRIES', $payload)) {
            $this->getConfigService()->setMaxTaskExecutionRetries((int)$payload['MAX_TASK_EXECUTION_RETRIES']);
        }

        if (array_key_exists('TASK_RUNNER_HALTED', $payload)) {
            $this->getConfigService()->setTaskRunnerHalted($payload['TASK_RUNNER_HALTED']);
        }

        if (array_key_exists('HARD_RESET', $payload)) {
            $this->hardReset();
        }

        return array('status' => 'success');
    }

    /**
     * Returns task items for current context in provided status.
     *
     * @param string $status
     *
     * @return array
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryClassException
     */
    protected function getItems($status)
    {
        $filter = new QueryFilter();
        $filter->where('context', Operators::EQUALS, ConfigurationManager::getInstance()->getContext())
            ->where('status', Operators::EQUALS, $status);

        $itemEntities = RepositoryRegistry::getQueueItemRepository()->select($filter);

        $items = array();
        foreach ($itemEntities as $itemEntity) {
            $items[] = $itemEntity->toArray();
        }

        return $items;
    }

    /**
     * Retrieves processes.
     *
     * @return Process[]
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function getProcesses()
    {
        $query = new QueryFilter();
        $query->setLimit(10);
        $repository = RepositoryRegistry::getRepository(Process::getClassName());
        $processes = $repository->select($query);
        $result = array();

        foreach ($processes as $process) {
            $result[] = $process->toArray();
        }

        return $result;
    }

    /**
     * Returns public urls
     *
     * @return array
     */
    protected function getPublicAvailableUrls()
    {
        return array(
            'ASYNC_URL' => $this->getConfigService()->getAsyncProcessUrl('/'),
        );
    }

    /**
     * @return int
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    protected function getGlobalMinLogLevel()
    {
        $config = $this->getMinLogLevelEntity();

        return $config ? $config->getValue() : Logger::ERROR;
    }

    /**
     * @return ConfigEntity
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    protected function getMinLogLevelEntity()
    {
        $filter = new QueryFilter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $filter->where('name', '=', 'minLogLevel');
        $filter->where('context', Operators::NULL);

        /** @var ConfigEntity $config */
        $config = $this->getConfigRepository()->selectOne($filter);

        return $config;
    }

    /**
     * @param $globalLogLevel
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    protected function saveGlobalMinLogLevel($globalLogLevel)
    {
        $existingGlobalLogLevel = $this->getMinLogLevelEntity();
        if ($existingGlobalLogLevel) {
            $existingGlobalLogLevel->setValue($globalLogLevel);
            $this->getConfigRepository()->update($existingGlobalLogLevel);
        } else {
            $newConfig = new ConfigEntity();
            $newConfig->setName('minLogLevel');
            $newConfig->setValue($globalLogLevel);
            $this->getConfigRepository()->save($newConfig);
        }
    }

    /**
     * Removes all data.
     */
    abstract protected function hardReset();

    /**
     * @return Configuration
     */
    protected function getConfigService()
    {
        if ($this->configService === null) {
            $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        }

        return $this->configService;
    }

    /**
     * @return AuthorizationService
     */
    protected function getAuthorizationService()
    {
        if ($this->authService === null) {
            $this->authService = ServiceRegister::getService(AuthorizationService::CLASS_NAME);
        }

        return $this->authService;
    }

    /**
     * @return RepositoryInterface
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function getConfigRepository()
    {
        return RepositoryRegistry::getRepository(ConfigEntity::getClassName());
    }
}