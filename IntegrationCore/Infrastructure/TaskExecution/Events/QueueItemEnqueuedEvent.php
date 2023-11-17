<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task;

/**
 * Class QueueItemEnqueuedEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events
 */
class QueueItemEnqueuedEvent extends BaseQueueItemEvent
{
    /**
     * @var string
     */
    protected $queueName;
    /**
     * @var Task
     */
    protected $task;
    /**
     * @var string
     */
    protected $context;
    /**
     * @var int
     */
    protected $priority;

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->getQueueItem()->getQueueName();
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->getQueueItem()->getTask();
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->getQueueItem()->getContext();
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->getQueueItem()->getPriority();
    }
}