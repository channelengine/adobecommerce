<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Listeners\StateTransition;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\OrderSync;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events\QueueStatusChangedEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueItemDeserializationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;

/**
 * Class OrderStateTransitionListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\Listeners\StateTransition
 */
class OrderStateTransitionListener
{
    /**
     * Handles QueueStatusChangedEvent.
     *
     * @param QueueStatusChangedEvent $event
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     * @throws QueueItemDeserializationException
     */
    public function handle(QueueStatusChangedEvent $event): void
    {
        $queueItem = $event->getQueueItem();
        $task = $queueItem->getTask();
        $stateService = new StateService();

        if (!($task instanceof OrderSync)) {
            return;
        }

        if ($queueItem->getStatus() === QueueItem::IN_PROGRESS && !$stateService->isManualOrderSyncInProgress()) {
            $stateService->setInitialSyncInProgress(true);
            $stateService->setOrderSyncInProgress(true);
        }

        if (in_array($queueItem->getStatus(), [QueueItem::COMPLETED, QueueItem::ABORTED, QueueItem::FAILED], true)) {
            $stateService->setOrderSyncInProgress(false);
            $stateService->setManualOrderSyncInProgress(false);

            if (!$stateService->isProductSyncInProgress()) {
                $stateService->setInitialSyncInProgress(false);
                $stateService->setOnboardingCompleted(true);
            }
        }
    }
}
