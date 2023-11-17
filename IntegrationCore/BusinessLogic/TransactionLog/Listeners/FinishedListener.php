<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events\QueueItemFinishedEvent;

/**
 * Class FinishedListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Listeners
 */
class FinishedListener extends UpdateListener
{
    /**
     * @var QueueItemFinishedEvent
     */
    protected $event;

    /**
     * @inheritdoc
     */
    protected function save()
    {
        $this->transactionLog->setCompletedTime($this->getTimeProvider()->getCurrentLocalTime());
        if ($finishTimestamp = $this->queueItem->getFinishTimestamp()) {
            $this->transactionLog->setCompletedTime(
                $this->getTimeProvider()->getDateTime($finishTimestamp)
            );
        }

        parent::save();
    }
}