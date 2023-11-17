<?php

/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsSyncConfigService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\SyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;

/**
 * Class ProductsSyncConfigService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products
 */
class ProductsSyncConfigService implements BaseService
{
    /**
     * @inheritDoc
     */
    public function get()
    {
        $filter = new QueryFilter();
        $filter->orderBy('id', QueryFilter::ORDER_DESC);

        return $this->getRepository()->selectOne($filter);
    }

    /**
     * @inheritDoc
     */
    public function set(SyncConfig $config)
    {
        $existing = $this->get();
        if($existing) {
            $config->setId($existing->getId());
            $this->getRepository()->update($config);

            return;
        }

        $this->getRepository()->save($config);
    }

    /**
     * Provides repository.
     *
     * @return RepositoryInterface
     *
     */
    protected function getRepository()
    {
        return RepositoryRegistry::getRepository(SyncConfig::getClassName());
    }
}