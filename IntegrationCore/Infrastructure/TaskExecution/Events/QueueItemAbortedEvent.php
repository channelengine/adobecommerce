<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;

/**
 * Class QueueItemAbortedEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events
 */
class QueueItemAbortedEvent extends BaseQueueItemEvent
{
    protected $abortDescription;

    /**
     * QueueItemAbortedEvent constructor.
     *
     * @param QueueItem $queueItem
     * @param $abortDescription
     */
    public function __construct(QueueItem $queueItem, $abortDescription)
    {
        parent::__construct($queueItem);
        $this->abortDescription = $abortDescription;
    }

    /**
     * @return mixed
     */
    public function getAbortDescription()
    {
        return $this->abortDescription;
    }
}