<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;

/**
 * Class NotificationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications
 */
class NotificationService implements BaseService
{
    /**
     * @inheritDoc
     */
    public function create(Notification $notification)
    {
        $notification->setIsRead(false);
        $notification->setCreatedAt($this->getTimeProvider()->getCurrentLocalTime());
        $this->getRepository()->save($notification);
    }

    /**
     * @inheritDoc
     */
    public function update(Notification $notification)
    {
        $this->getRepository()->update($notification);
    }

    /**
     * @inheritDoc
     */
    public function delete(Notification $notification)
    {
        $this->getRepository()->delete($notification);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $filter = new QueryFilter();
        $filter->where('id', Operators::EQUALS, $id);

        return $this->getRepository()->selectOne($filter);
    }

    /**
     * @inheritDoc
     */
    public function find(array $query, $offset = 0, $limit = 1000)
    {
        $queryFilter = new QueryFilter();
	    $queryFilter->setLimit($limit);
	    $queryFilter->setOffset($offset);
        $queryFilter->orderBy('id', QueryFilter::ORDER_DESC);

        foreach ($query as $column => $value) {
            if ($value === null) {
                $queryFilter->where($column, Operators::NULL);
            } else {
                $queryFilter->where($column, Operators::EQUALS, $value);
            }
        }

        return $this->getRepository()->select($queryFilter);
    }

    /**
     * @inheritDoc
     */
    public function countNotRead(array $query = [])
    {
        $filter = new QueryFilter();

        foreach ($query as $column => $value) {
            if ($value === null) {
                $filter->where($column, Operators::NULL);
            } else {
                $filter->where($column, Operators::EQUALS, $value);
            }
        }

        $filter->where('isRead', Operators::EQUALS, false);

        return $this->getRepository()->count($filter);
    }

    private function getRepository()
    {
        return RepositoryRegistry::getRepository(Notification::getClassName());
    }

    /**
     * @return TimeProvider
     */
    private function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
}