<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks;

/**
 * Class ProductsReplaceTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks
 */
class ProductsReplaceTask extends BaseSyncProductsTask
{
    protected function exportProducts(&$batchOfProducts, $syncedProducts)
    {
        $syncConfig = $this->getProductsSyncConfigService()->get();
        if( $syncConfig === null || $syncConfig->isEnabledStockSync() ) {
            foreach ($batchOfProducts as $merchantProductNo => $data) {
                $this->getProductsProxy()->purgeAndReplaceProducts($merchantProductNo, $data);
            }
        } else {
            foreach ($batchOfProducts as $merchantProductNo => $data) {
                $this->getProductsProxy()->purgeAndReplaceProductsWithoutStock($merchantProductNo, $data);
            }
        }

        $this->syncedNumber = $syncedProducts;
    }
}
