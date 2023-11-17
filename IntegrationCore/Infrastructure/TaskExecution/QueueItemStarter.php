<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Interfaces\Serializable;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\AbortTaskExecutionException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\ExecutionRequirementsNotMetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\Runnable;
use Exception;

/**
 * Class QueueItemStarter
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution
 */
class QueueItemStarter implements Runnable
{
    /**
     * Id of queue item to start.
     *
     * @var int
     */
    private $queueItemId;
    /**
     * Service instance.
     *
     * @var QueueService
     */
    private $queueService;
    /**
     * Service instance.
     *
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * QueueItemStarter constructor.
     *
     * @param int $queueItemId Id of queue item to start.
     */
    public function __construct($queueItemId)
    {
        $this->queueItemId = $queueItemId;
    }

    /**
     * Transforms array into an serializable object,
     *
     * @param array $array Data that is used to instantiate serializable object.
     *
     * @return Serializable
     *      Instance of serialized object.
     */
    public static function fromArray(array $array)
    {
        return new static($array['queue_item_id']);
    }

    /**
     * Transforms serializable object into an array.
     *
     * @return array Array representation of a serializable object.
     */
    public function toArray()
    {
        return array('queue_item_id' => $this->queueItemId);
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return Serializer::serialize(array($this->queueItemId));
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list($this->queueItemId) = Serializer::unserialize($serialized);
    }

    /**
     * Starts runnable run logic.
     */
    public function run()
    {
        /** @var QueueItem $queueItem */
        $queueItem = $this->fetchItem();

        if ($queueItem === null || ($queueItem->getStatus() !== QueueItem::QUEUED)) {
            Logger::logDebug(
                'Fail to start task execution because task no longer exists or it is not in queued state anymore.',
                'Core',
                array(
                    'TaskId' => $this->getQueueItemId(),
                    'Status' => $queueItem !== null ? $queueItem->getStatus() : 'unknown',
                )
            );

            return;
        }

        $queueService = $this->getQueueService();
        try {
            $this->getConfigManager()->setContext($queueItem->getContext());
            $queueService->validateExecutionRequirements($queueItem);
            $queueService->start($queueItem);
        } catch (QueueStorageUnavailableException $e) {
            Logger::logInfo($e->getMessage(), 'Core', array('trace' => $e->getTraceAsString()));
        } catch (ExecutionRequirementsNotMetException $e) {
            $id = $queueItem->getId();
            Logger::logWarning(
                "Execution requirements not met for queue item [$id] because:" .
                $e->getMessage(),
                'Core',
                array('ExceptionTrace' => $e->getTraceAsString())
            );
        } catch (AbortTaskExecutionException $exception) {
            $queueService->abort($queueItem, $exception->getMessage());
        } catch (Exception $ex) {
            if (QueueItem::IN_PROGRESS === $queueItem->getStatus()) {
                $queueService->fail($queueItem, $ex->getMessage());
            }
            $context = array(
                'TaskId' => $this->getQueueItemId(),
                'ExceptionMessage' => $ex->getMessage(),
                'ExceptionTrace' => $ex->getTraceAsString(),
            );

            Logger::logError("Fail to start task execution because: {$ex->getMessage()}.", 'Core', $context);
        }
    }

    /**
     * Gets id of a queue item that will be run.
     *
     * @return int Id of queue item to run.
     */
    public function getQueueItemId()
    {
        return $this->queueItemId;
    }

    /**
     * Gets Queue item.
     *
     * @return QueueItem|null Queue item if found; otherwise, null.
     */
    private function fetchItem()
    {
        try {
            $queueItem = $this->getQueueService()->find($this->queueItemId);
        } catch (Exception $ex) {
            return null;
        }

        return $queueItem;
    }

    /**
     * Gets Queue service instance.
     *
     * @return QueueService Service instance.
     */
    private function getQueueService()
    {
        if ($this->queueService === null) {
            $this->queueService = ServiceRegister::getService(QueueService::CLASS_NAME);
        }

        return $this->queueService;
    }

    /**
     * Gets configuration service instance.
     *
     * @return ConfigurationManager Service instance.
     */
    private function getConfigManager()
    {
        if ($this->configurationManager === null) {
            $this->configurationManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);
        }

        return $this->configurationManager;
    }
}
