<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;

/**
 * Class QueueItemFailedEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events
 */
class QueueItemFailedEvent extends BaseQueueItemEvent
{
    /**
     * @var string
     */
    protected $failureDescription;

    /**
     * QueueItemFailedEvent constructor.
     *
     * @param QueueItem $queueItem
     * @param string $failureDescription
     */
    public function __construct(QueueItem $queueItem, $failureDescription)
    {
        parent::__construct($queueItem);
        $this->failureDescription = $failureDescription;
    }

    /**
     * @return string
     */
    public function getFailureDescription()
    {
        return $this->failureDescription;
    }
}