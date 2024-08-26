<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\StockServiceInterface;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class StockMSIDisabledService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class StockMSIDisabledService implements StockServiceInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterface;

    /**
     * @param StockRegistryInterface $stockRegistryInterface
     */
    public function __construct(StockRegistryInterface $stockRegistryInterface)
    {
        $this->stockRegistryInterface = $stockRegistryInterface;
    }

    /**
     * Retrieve product stock quantity.
     *
     * @param ProductInterface $product
     *
     * @return int
     * @throws QueryFilterInvalidParamException
     */
    public function getStock(ProductInterface $product): int
    {
        $stockSettings = $this->getStockService()->getStockSettings();

        if (!$stockSettings || !$stockSettings->isEnableStockSync()) {
            return 0;
        }

        $stockItem = $this->stockRegistryInterface->getStockItem($product->getId());
        $quantity = $stockItem->getQty();

        if ($quantity && $quantity >= 0) {
            return $quantity;
        }

        return $stockSettings->getQuantity();
    }

    /**
     * @return StockSettingsService
     */
    private function getStockService(): StockSettingsService
    {
        return ServiceRegister::getService(StockSettingsService::class);
    }
}
