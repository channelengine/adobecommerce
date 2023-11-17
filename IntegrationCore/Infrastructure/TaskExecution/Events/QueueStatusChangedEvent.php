<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\Events\Event;

/**
 * Class QueueStatusChangedEvent.
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Scheduler
 */
class QueueStatusChangedEvent extends Event
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Queue item.
     *
     * @var QueueItem
     */
    private $queueItem;
    /**
     * Previous state of queue item.
     *
     * @var string
     */
    private $previousState;

    /**
     * TaskProgressEvent constructor.
     *
     * @param QueueItem $queueItem Queue item with changed status.
     * @param string $previousState Previous state. MUST be one of the states defined as constants in @see QueueItem.
     */
    public function __construct(QueueItem $queueItem, $previousState)
    {
        $this->queueItem = $queueItem;
        $this->previousState = $previousState;
    }

    /**
     * Gets Queue item.
     *
     * @return QueueItem Queue item.
     */
    public function getQueueItem()
    {
        return $this->queueItem;
    }

    /**
     * Gets previous state.
     *
     * @return string Previous state..
     */
    public function getPreviousState()
    {
        return $this->previousState;
    }
}
