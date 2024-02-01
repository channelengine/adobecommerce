<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\ManualSync;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\InitialSync\ProductSync;

class ProductsResyncTask extends ProductSync
{
    protected function getSubJobInstance($ids)
    {
        return new ProductsResyncJobTask($ids);
    }
}
