<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Transformers\ApiProductTransformer;

/**
 * Class ProductsUpsertTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks
 */
class ProductsUpsertTask extends BaseSyncProductsTask
{
    protected function exportProducts(&$batchOfProducts, $syncedProducts)
    {
        $syncConfig = $this->getProductsSyncConfigService()->get();
        if( $syncConfig === null || $syncConfig->isEnabledStockSync() ) {
            $this->getProductsProxy()->upload($batchOfProducts);
        } else {
            $this->getProductsProxy()->uploadWithoutStock($batchOfProducts);
        }

        $this->syncedNumber = $syncedProducts;
    }

    protected function getProductExportData(&$batchOfProducts, $product) {
        $syncConfig = $this->getProductsSyncConfigService()->get();
        $threeLevelSyncStatus =  $syncConfig ? $syncConfig->getThreeLevelSyncStatus() : false;

        if ($threeLevelSyncStatus === false) {
            $batchOfProducts = array_merge($batchOfProducts, ApiProductTransformer::transformProductToTwoLevel($product));
        } else {
            $batchOfProducts = count($product->getVariants()) === 0
                ? array_merge($batchOfProducts, ApiProductTransformer::transformSimpleProductToThreeLevel($product))
                : array_merge($batchOfProducts, ApiProductTransformer::transformProductWithChildrenToThreeLevel($product));
        }
    }
}
