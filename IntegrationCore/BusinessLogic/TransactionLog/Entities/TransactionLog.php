<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;

class TransactionLog extends Entity
{
    const CLASS_NAME = __CLASS__;

    protected $fields = [
        'id',
        'executionId',
        'context',
        'taskType',
        'status',
        'startTime',
        'completedTime',
        'synchronizedEntities',
	    'totalCount',
        'hasErrors'
    ];

    /**
     * @var int
     */
    protected $executionId;
    /**
     * @var string
     */
    protected $taskType;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var DateTime
     */
    protected $startTime;
    /**
     * @var DateTime
     */
    protected $completedTime;
    /**
     * @var int
     */
    protected $synchronizedEntities;
	/**
	 * @var int
	 */
	protected $totalCount;
    /**
     * @var boolean
     */
    protected $hasErrors;
    /**
     * @var string
     */
    protected $context;

    /**
     * @return int
     */
    public function getExecutionId()
    {
        return $this->executionId;
    }

    /**
     * @param int $executionId
     */
    public function setExecutionId($executionId)
    {
        $this->executionId = $executionId;
    }

    /**
     * @return string
     */
    public function getTaskType()
    {
        return $this->taskType;
    }

    /**
     * @param string $taskType
     */
    public function setTaskType($taskType)
    {
        $this->taskType = $taskType;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return DateTime
     */
    public function getCompletedTime()
    {
        return $this->completedTime;
    }

    /**
     * @param DateTime $completedTime
     */
    public function setCompletedTime($completedTime)
    {
        $this->completedTime = $completedTime;
    }

    /**
     * @return int
     */
    public function getSynchronizedEntities()
    {
        return $this->synchronizedEntities;
    }

    /**
     * @param int $synchronizedEntities
     */
    public function setSynchronizedEntities($synchronizedEntities)
    {
        $this->synchronizedEntities = $synchronizedEntities;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * @param bool $hasErrors
     */
    public function setHasErrors($hasErrors)
    {
        $this->hasErrors = $hasErrors;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function getConfig()
    {
        $map = new IndexMap();
        $map->addIntegerIndex('executionId')
            ->addStringIndex('context')
            ->addStringIndex('taskType')
            ->addStringIndex('status')
            ->addDateTimeIndex('startTime');

        return new EntityConfiguration($map, 'TransactionLog');
    }

    public function toArray()
    {
        $result = parent::toArray();

        $result['startTime'] = $this->getTimeProvider()->serializeDate($this->getStartTime());
        $result['completedTime'] = $this->getTimeProvider()->serializeDate($this->getCompletedTime());

        return $result;
    }

    public function inflate(array $data)
    {
        parent::inflate($data);

        $this->setStartTime($this->getTimeProvider()->deserializeDateString($data['startTime']));
        $this->setCompletedTime($this->getTimeProvider()->deserializeDateString($data['completedTime']));
    }

    /**
     * @return TimeProvider
     */
    private function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
}