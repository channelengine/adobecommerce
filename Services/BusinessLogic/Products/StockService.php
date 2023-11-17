<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\DTO\StockSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Inventory\Model\Source\Command\GetSourcesAssignedToStockOrderedByPriority;

/**
 * Class StockService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class StockService
{
    /**
     * @var GetSourceItemsBySku
     */
    private $getSourceItemsBySku;
    /**
     * @var StockSettings
     */
    private $stockSettings;
    private $getSalableQuantityDataBySku;

    /**
     * @var GetSourcesAssignedToStockOrderedByPriority
     */
    private $getSourcesAssignedToStockOrderedByPriority;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @param  GetSourceItemsBySku  $getSourceItemsBySku
     * @param  \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku  $getSalableQuantityDataBySku
     * @param  \Magento\Inventory\Model\Source\Command\GetSourcesAssignedToStockOrderedByPriority  $getSourcesAssignedToStockOrderedByPriority
     * @param  \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface  $isSourceItemManagementAllowedForProductType
     */
    public function __construct(
        GetSourceItemsBySku $getSourceItemsBySku,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        GetSourcesAssignedToStockOrderedByPriority $getSourcesAssignedToStockOrderedByPriority,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
    ) {
        $this->getSourceItemsBySku                         = $getSourceItemsBySku;
        $this->getSalableQuantityDataBySku                 = $getSalableQuantityDataBySku;
        $this->getSourcesAssignedToStockOrderedByPriority  = $getSourcesAssignedToStockOrderedByPriority;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
    }

    /**
     * Retrieve product stock quantity.
     *
     * @param ProductInterface $product
     *
     * @return int
     *
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws QueryFilterInvalidParamException
     * @throws SkuIsNotAssignedToStockException
     */
    public function getStock(ProductInterface $product): int
    {
        $stockSettings = $this->getStockSettings();
        $storeStocks = $this->getSourceItemsBySku->execute($product->getSku());
        $quantity = 0;

        if (!$stockSettings->isEnableStockSync()) {
            return $quantity;
        }

        if (!$storeStocks || !$this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId())) {
            return $stockSettings->getQuantity();
        }

        $inInventory = false;
        $stocks = $this->getSalableQuantityDataBySku->execute($product->getSku());
        foreach ($stocks as $stock) {
            $sources = $this->getSourcesAssignedToStockByPriority($stock['stock_id']);
            foreach ($sources as $source) {
                if (in_array($source['source_code'], $stockSettings->getInventories(), true)) {
                    $inInventory = true;
                    $quantity += $stock['qty'];
                }
            }
        }

        if (!$inInventory) {
            return $stockSettings->getQuantity();
        }

        return $quantity;
    }

    /**
     * @param $stockId
     * @return array
     * @throws InputException
     * @throws LocalizedException
     */
    private function getSourcesAssignedToStockByPriority($stockId): array
    {
        $sources = [];
        foreach ($this->getSourcesAssignedToStockOrderedByPriority->execute($stockId) as $item) {
            $sources[] = $item->getData();
        }

        return $sources;
    }

    /**
     * @return StockSettings
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getStockSettings(): StockSettings
    {
        if ($this->stockSettings === null) {
            $this->stockSettings = $this->getStockService()->getStockSettings();
        }

        return $this->stockSettings;
    }

    /**
     * @return StockSettingsService
     */
    private function getStockService(): StockSettingsService
    {
        return ServiceRegister::getService(StockSettingsService::class);
    }
}
