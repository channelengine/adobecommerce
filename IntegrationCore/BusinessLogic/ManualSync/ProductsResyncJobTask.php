<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\ManualSync;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductsPurgeTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductsUpsertTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalComposite;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task;

class ProductsResyncJobTask extends TransactionalComposite
{
    /**
     * @var array
     */
    protected $productIds;

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['productIds'] = $this->productIds;

        return $array;
    }

    /**
     * @param array $serializedData
     * @return \ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Interfaces\Serializable
     */
    public static function fromArray(array $serializedData)
    {
        $entity = parent::fromArray($serializedData);
        $entity->productIds = $serializedData['productIds'];

        return $entity;
    }

    public function __construct($productIds = [])
    {
        parent::__construct($this->getSubTaskList());
        $this->productIds = $productIds;
    }

    protected function createSubTask($taskKey)
    {
        if ($taskKey === ProductsUpsertTask::class) {
            return new ProductsUpsertTask($this->productIds);
        }

        return new ProductsPurgeTask($this->productIds);
    }

    protected function getSubTaskList()
    {
        return [
            ProductsPurgeTask::class => 30,
            ProductsUpsertTask::class => 70,
        ];
    }
}
