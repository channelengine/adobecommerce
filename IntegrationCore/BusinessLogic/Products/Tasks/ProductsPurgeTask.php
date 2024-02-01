<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Product;

/**
 * Class ProductsPurgeTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks
 */
class ProductsPurgeTask extends BaseSyncProductsTask
{
    protected function getProductExportData(&$batchOfProducts, $product)
    {
        $batchOfProducts[] = $product->getId();
    }

    protected function exportProducts(&$batchOfProducts, $syncedProducts)
    {
        $this->getProductsProxy()->purgeProducts($batchOfProducts);
        $this->syncedNumber = $syncedProducts;
    }

    protected function fetchProductsToExport(array $batch)
    {
        $products = [];

        foreach ($batch as $id){
            $products[] = new Product($id, 0, 0, '');
        }

        return $products;
    }
}