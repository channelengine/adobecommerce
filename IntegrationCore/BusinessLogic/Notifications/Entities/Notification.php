<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;

/**
 * Class Notification
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities
 */
class Notification extends Entity
{
    const CLASS_NAME = __CLASS__;
    protected $fields = ['id', 'transactionLogId', 'notificationContext', 'message', 'arguments', 'isRead', 'context'];

    /**
     * @var int
     */
    protected $transactionLogId;
    /**
     * @var string @see \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\Context
     */
    protected $notificationContext;
    /**
     * @var string
     */
    protected $message;
    /**
     * @var array
     */
    protected $arguments = [];
    /**
     * @var bool
     */
    protected $isRead;
    /**
     * @var DateTime
     */
    protected $createdAt;
    /**
     * @var string
     */
    protected $context;

    /**
     * @return int
     */
    public function getTransactionLogId()
    {
        return $this->transactionLogId;
    }

    /**
     * @param int $transactionLogId
     */
    public function setTransactionLogId($transactionLogId)
    {
        $this->transactionLogId = $transactionLogId;
    }

    /**
     * @return string
     */
    public function getNotificationContext()
    {
        return $this->notificationContext;
    }

    /**
     * @param string $notificationContext
     */
    public function setNotificationContext($notificationContext)
    {
        $this->notificationContext = $notificationContext;
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
        $this->arguments = $arguments;
    }

    /**
     * @return bool
     */
    public function isRead()
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
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

    /**
     * @inheritDoc
     */
    public function inflate(array $data)
    {
        parent::inflate($data);
        $this->setCreatedAt($this->getTimeProvider()->deserializeDateString($data['createdAt']));
    }

    public function getConfig()
    {
        $map = new IndexMap();
        $map->addIntegerIndex('transactionLogId')
            ->addStringIndex('context')
            ->addStringIndex('notificationContext')
            ->addBooleanIndex('isRead')
            ->addDateTimeIndex('createdAt');

        return new EntityConfiguration($map, 'Notification');
    }

    /**
     * @return TimeProvider
     */
    private function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
}