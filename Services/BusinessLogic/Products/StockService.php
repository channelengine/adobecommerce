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
use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesAdminUi\Model\GetStockSourceLinksBySourceCode;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

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

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var GetStockSourceLinksBySourceCode
     */
    private $getStockSourceLinksBySourceCode;

    /**
     * @var GetProductSalableQtyInterface
     */
    private $productSalableQty;

    /**
     * @param GetSourceItemsBySku $getSourceItemsBySku
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param GetStockSourceLinksBySourceCode $getStockSourceLinksBySourceCode
     * @param GetProductSalableQtyInterface $productSalableQty
     */
    public function __construct(
        GetSourceItemsBySku $getSourceItemsBySku,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        GetStockSourceLinksBySourceCode $getStockSourceLinksBySourceCode,
        GetProductSalableQtyInterface $productSalableQty
    ) {
        $this->getSourceItemsBySku                         = $getSourceItemsBySku;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->getStockSourceLinksBySourceCode = $getStockSourceLinksBySourceCode;
        $this->productSalableQty = $productSalableQty;
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

        foreach ($stockSettings->getInventories() as $inventory) {
            /** @var StockSourceLinkInterface $stockSourceLink */
            $stockSourceLinks = $this->getStockSourceLinksBySourceCode->execute($inventory);
            foreach ($stockSourceLinks as $stockSourceLink) {
                $quantity += $this->productSalableQty->execute($product->getSku(), $stockSourceLink->getStockId());
            }
        }

        return $quantity;
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
