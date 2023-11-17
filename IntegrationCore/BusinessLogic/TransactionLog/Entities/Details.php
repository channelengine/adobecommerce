<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;

class Details extends Entity
{
    const CLASS_NAME = __CLASS__;

    protected $fields = ['id', 'logId', 'message', 'arguments', 'createdAt', 'context'];

    /**
     * @var int
     */
    protected $logId;
    /**
     * @var string
     */
    protected $message;
    /**
     * @var array
     */
    protected $arguments = [];
    /**
     * @var \DateTime
     */
    protected $createdAt;
    /**
     * @var string
     */
    protected $context;

    /**
     * @return int
     */
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * @param int $logId
     */
    public function setLogId($logId)
    {
        $this->logId = $logId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments ?: [];
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
     * @inheritDoc
     */
    public function toArray()
    {
        $result = parent::toArray();
        $result['createdAt'] = $this->getTimeProvider()->serializeDate($this->getCreatedAt());

        return $result;
    }

    public function inflate(array $data)
    {
        parent::inflate($data);
        $this->createdAt = $this->getTimeProvider()->deserializeDateString($data['createdAt']);
    }

    public function getConfig()
    {
        $map = new IndexMap();
        $map->addIntegerIndex('logId')
            ->addStringIndex('context');
        $map->addDateTimeIndex('createdAt');

        return new EntityConfiguration($map, 'Details');
    }

    /**
     * @return TimeProvider
     */
    private function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
}