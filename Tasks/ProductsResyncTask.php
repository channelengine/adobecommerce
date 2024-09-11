<?php

namespace ChannelEngine\ChannelEngineIntegration\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\ManualSync\ProductsResyncTask as BaseProductsResyncTask;

class ProductsResyncTask extends BaseProductsResyncTask
{
    protected function getSubJobInstance($ids)
    {
        return new ProductsResyncJobTask($ids);
    }
}
