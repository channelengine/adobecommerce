<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductsUpsertTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalOrchestrator;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Composite\ExecutionDetails;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;

class ProductSync extends TransactionalOrchestrator
{
    const PRODUCTS_PER_BATCH = 5000;

    protected $page = 0;

    public function toArray()
    {
        $result = parent::toArray();
        $result['page'] = $this->page;

        return $result;
    }

    public static function fromArray(array $array)
    {
        $entity = parent::fromArray($array);
        if (property_exists($entity, 'page') && array_key_exists('page', $array)) {
            $entity->page = $array['page'];
        }

        return $entity;
    }

    public function serialize()
    {
        return Serializer::serialize([
            'parent' => parent::serialize(),
            'page' => $this->page
        ]);
    }

    public function unserialize($serialized)
    {
        $unserialized = Serializer::unserialize($serialized);
        parent::unserialize($unserialized['parent']);
        $this->page = $unserialized['page'];
    }

    /**
     * Creates subtask.
     *
     * @return ExecutionDetails | null
     *
     * @throws QueueStorageUnavailableException
     */
    protected function getSubTask()
    {
        $ids = $this->getService()->getProductIds($this->page, static::PRODUCTS_PER_BATCH);
        if (empty($ids)) {
            return null;
        }

        try {
            if (ConfigurationManager::getInstance()->getConfigValue('syncProducts', 1)) {
                $this->page++;

                return $this->createSubJob($this->getSubJobInstance($ids));
            }
        } catch (QueryFilterInvalidParamException $exception) {
            Logger::logError($exception->getMessage());

            return null;
        }

        return null;
    }

    protected function getSubJobInstance($ids)
    {
        return new ProductsUpsertTask($ids);
    }

    /**
     * Provides product service.
     *
     * @return ProductsService
     */
    protected function getService()
    {
        return ServiceRegister::getService(ProductsService::class);
    }
}