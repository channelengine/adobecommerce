<?php

/** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use DateTime;
use Exception;

/**
 * Class OrdersConfigurationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\OrdersConfiguration
 */
class OrdersConfigurationService
{
    const CLASS_NAME = __CLASS__;

    const INCLUDE_FULL = 'include_full';
    const EXCLUDE_FULL = 'exclude_full';

    /**
     * Retrieves saved OrderSyncConfig.
     *
     * @return OrderSyncConfig | null
     */
    public function getOrderSyncConfig()
    {
        $filter = new QueryFilter();
        $filter->orderBy('id', QueryFilter::ORDER_DESC);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getOrderSyncConfigRepository()->selectOne($filter);
    }

    /**
     * Saves new OrderSyncConfig.
     *
     * @param OrderSyncConfig $syncConfig
     */
    public function saveOrderSyncConfig(OrderSyncConfig $syncConfig)
    {
        $existing = $this->getOrderSyncConfig();
        if ($existing) {
            $syncConfig->setId($existing->getId());
            $this->getOrderSyncConfigRepository()->update($syncConfig);

            return;
        }

        $this->getOrderSyncConfigRepository()->save($syncConfig);
    }

    /**
     * Retrieves closed orders last sync time.
     *
     * @return DateTime
     *
     * @throws RepositoryNotRegisteredException
     * @throws Exception
     */
    public function getClosedOrdersSyncTime()
    {
        return $this->getSyncTime('closedOrdersLastSyncTime');
    }

    /**
     * Sets closed orders last sync time.
     *
     * @param DateTime $lastSyncTime
     *
     * @throws RepositoryNotRegisteredException
     */
    public function setClosedOrdersSyncTime(DateTime $lastSyncTime)
    {
        $this->saveSyncTime($lastSyncTime, 'closedOrdersLastSyncTime');
    }

    /**
     * Retrieves last order sync check time.
     *
     * @return DateTime
     *
     * @throws RepositoryNotRegisteredException
     * @throws Exception
     */
    public function getLastOrderSyncCheckTime()
    {
        return $this->getSyncTime('lastOrderSyncCheckTime') ?: (new DateTime())->setTimestamp(0);
    }

    /**
     * Sets last order sync check time.
     *
     * @param DateTime $lastSyncTime
     *
     * @throws RepositoryNotRegisteredException
     */
    public function setLastOrderSyncCheckTime(DateTime $lastSyncTime)
    {
        $this->saveSyncTime($lastSyncTime, 'lastOrderSyncCheckTime');
    }

    /**
     * @param string $key
     *
     * @return DateTime
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueryFilterInvalidParamException
     */
    protected function getSyncTime($key)
    {
        $filter = new QueryFilter();
        $filter->where('key', Operators::EQUALS, $key);
        $filter->where('context', '=', ConfigurationManager::getInstance()->getContext());

        /** @var OrdersConfigEntity | null $entity */
        $entity = $this->getOrdersConfigEntityRepository()->selectOne($filter);

        if (!$entity) {
            return null;
        }

        return new DateTime($entity->getValue());
    }

    /**
     * @param DateTime $syncTime
     * @param string $key
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function saveSyncTime(DateTime $syncTime, $key)
    {
        $ordersConfigEntity = new OrdersConfigEntity();
        $queryFilter = new QueryFilter();
        $queryFilter->where('key', Operators::EQUALS, $key);
        $queryFilter->where('context', '=', ConfigurationManager::getInstance()->getContext());
        $entity = $this->getOrdersConfigEntityRepository()->selectOne($queryFilter);

        if ($entity) {
            $ordersConfigEntity = $entity;
            $ordersConfigEntity->setValue($syncTime->format('Y-m-d H:i:s'));
            $this->getOrdersConfigEntityRepository()->update($ordersConfigEntity);
        }

        $ordersConfigEntity->setContext(ConfigurationManager::getInstance()->getContext());
        $ordersConfigEntity->setKey($key);
        $ordersConfigEntity->setValue($syncTime->format('Y-m-d H:i:s'));

        $this->getOrdersConfigEntityRepository()->save($ordersConfigEntity);
    }

    /**
     * Retrieves OrdersConfigEntity repository.
     *
     * @return RepositoryInterface
     *
     * @throws RepositoryNotRegisteredException
     */
    protected function getOrdersConfigEntityRepository()
    {
        return RepositoryRegistry::getRepository(OrdersConfigEntity::CLASS_NAME);
    }

    /**
     * Retrieves OrderSyncConfig repository.
     *
     * @return RepositoryInterface
     */
    protected function getOrderSyncConfigRepository()
    {
        return RepositoryRegistry::getRepository(OrderSyncConfig::class);
    }
}