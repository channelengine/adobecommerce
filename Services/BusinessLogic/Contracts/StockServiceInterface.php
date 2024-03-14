<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface StockServiceInterface
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts
 */
interface StockServiceInterface
{
    /**
     * Retrieve product stock quantity.
     *
     * @param ProductInterface $product
     *
     * @return int
     */
    public function getStock(ProductInterface $product): int;
}
