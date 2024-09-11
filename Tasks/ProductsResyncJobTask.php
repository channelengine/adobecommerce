<?php

namespace ChannelEngine\ChannelEngineIntegration\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\ManualSync\ProductsResyncJobTask as BaseProductsResyncJobTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductsUpsertTask;

class ProductsResyncJobTask extends BaseProductsResyncJobTask
{
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
