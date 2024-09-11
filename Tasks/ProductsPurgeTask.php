<?php

namespace ChannelEngine\ChannelEngineIntegration\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Product;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductsPurgeTask as BaseProductsPurgeTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products\ProductService;

class ProductsPurgeTask extends BaseProductsPurgeTask
{
    /**
     * @param array $batch
     * @return array
     * @throws QueryFilterInvalidParamException
     */
    protected function fetchProductsToExport(array $batch): array
    {
        $ids = [];
        $products = [];
        $mappings = $this->getMappingsService()->getAttributeMappings();
        if ($mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_SKU) {
            $magentoProducts = $this->getProductService()->getMagentoProducts($batch);
            foreach ($magentoProducts as $product){
                $ids[] = $product->getSku();
            }
        } else {
            $ids = $batch;
        }

        foreach ($ids as $id){
            $products[] = new Product($id, 0, 0, '');
        }

        return $products;
    }

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }

    /**
     * @return ProductService
     */
    private function getProductService(): ProductService
    {
        return ServiceRegister::getService(ProductsService::class);
    }
}
