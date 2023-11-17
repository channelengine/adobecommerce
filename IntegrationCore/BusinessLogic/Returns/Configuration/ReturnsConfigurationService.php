<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Configuration;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use DateTime;
use Exception;

/**
 * Class ReturnsConfigurationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Configuration
 */
class ReturnsConfigurationService
{
    /**
     * Retrieves returns last sync time.
     *
     * @return DateTime
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    public function getReturnsLastSyncTime()
    {
        return $this->getSyncTime('lastReturnsSyncTime');
    }

    /**
     * Sets returns last sync time.
     *
     * @param DateTime $lastSyncTime
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    public function setReturnsLastSyncTime(DateTime $lastSyncTime)
    {
        $this->saveSyncTime($lastSyncTime, 'lastReturnsSyncTime');
    }

    /**
     * @param string $key
     *
     * @return DateTime
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueryFilterInvalidParamException
     * @throws Exception
     */
    protected function getSyncTime($key)
    {
        $filter = new QueryFilter();
        $filter->where('key', Operators::EQUALS, $key);

        /** @var ReturnsConfigEntity | null $entity */
        $entity = $this->getReturnsConfigRepository()->selectOne($filter);

        if (!$entity) {
            return (new DateTime())->setTimestamp(0);
        }

        return new DateTime($entity->getValue());
    }

    /**
     * @param DateTime $syncTime
     * @param string $key
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueryFilterInvalidParamException
     */
    protected function saveSyncTime(DateTime $syncTime, $key)
    {
        $ordersConfigEntity = new ReturnsConfigEntity();
        $queryFilter = new QueryFilter();
        $queryFilter->where('key', Operators::EQUALS, $key);
        $entity = $this->getReturnsConfigRepository()->selectOne($queryFilter);

        if ($entity) {
            $entity->setValue($syncTime->format('Y-m-d H:i:s'));
            $this->getReturnsConfigRepository()->update($entity);

            return;
        }

        $ordersConfigEntity->setKey($key);
        $ordersConfigEntity->setValue($syncTime->format('Y-m-d H:i:s'));

        $this->getReturnsConfigRepository()->save($ordersConfigEntity);
    }

    /**
     * @return RepositoryInterface
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function getReturnsConfigRepository()
    {
        return RepositoryRegistry::getRepository(ReturnsConfigEntity::getClassName());
    }
}