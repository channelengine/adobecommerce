<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Repositories;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;

/**
 * Interface ProductEventRepository
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Repositories
 */
interface ProductEventRepository extends RepositoryInterface
{
    /**
     * Deletes multiple entities and returns success flag.
     *
     * @param ProductEvent[] $entities
     *
     * @return bool TRUE if operation succeeded; otherwise, FALSE.
     */
    public function batchDelete(array $entities);
}