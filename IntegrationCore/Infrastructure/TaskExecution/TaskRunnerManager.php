<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\TaskRunnerManager as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\TaskRunnerWakeup;

class TaskRunnerManager implements BaseService
{
    /**
     * @var Configuration
     */
    protected $configuration;
    /**
     * @var TaskRunnerWakeup
     */
    protected $tasRunnerWakeupService;

    /**
     * Halts task runner.
     */
    public function halt()
    {
        $this->getConfiguration()->setTaskRunnerHalted(true);
    }

    /**
     * Resumes task execution.
     */
    public function resume()
    {
        $this->getConfiguration()->setTaskRunnerHalted(false);
        $this->getTaskRunnerWakeupService()->wakeup();
    }

    /**
     * Retrieves configuration.
     *
     * @return Configuration Configuration instance.
     */
    protected function getConfiguration()
    {
        if ($this->configuration === null) {
            $this->configuration = ServiceRegister::getService(Configuration::CLASS_NAME);
        }

        return $this->configuration;
    }

    /**
     * Retrieves task runner wakeup service.
     *
     * @return TaskRunnerWakeup Task runner wakeup instance.
     */
    protected function getTaskRunnerWakeupService()
    {
        if ($this->tasRunnerWakeupService === null) {
            $this->tasRunnerWakeupService = ServiceRegister::getService(TaskRunnerWakeup::CLASS_NAME);
        }

        return $this->tasRunnerWakeupService;
    }
}