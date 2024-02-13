<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\DTO\PriceSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class PriceService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class PriceService
{
    /**
     * @var PriceSettings
     */
    private $priceSettings;

    /**
     * Retrieves product price.
     *
     * @param ProductInterface $product
     *
     * @return float|int|mixed
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getProductPrice(ProductInterface $product)
    {
        $priceSettings = $this->getPriceSettings();

        if ($priceSettings->isGroupPricing()) {
            $tierPrices = $product->getTierPrices();

            foreach ($tierPrices as $price) {
                if ((string)$price->getCustomerGroupId() === (string)$priceSettings->getCustomerGroup() &&
                    (float)$price->getQty() === (float)$priceSettings->getQuantity()) {
                    return (float)$price->getValue();
                }
            }

            return null;
        }

        if ($priceSettings->getPriceAttribute() === 'FINAL_PRICE') {
            return $product->getFinalPrice();
        }

        if ($priceSettings->getPriceAttribute() === 'price') {
            return $product->getPrice();
        }

        $priceAttribute = $product->getCustomAttribute($priceSettings->getPriceAttribute());

        return $priceAttribute ? (float)$priceAttribute->getValue() : null;
    }

    /**
     * @return PriceSettings
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getPriceSettings(): PriceSettings
    {
        if ($this->priceSettings === null) {
            $this->priceSettings = $this->getPriceService()->getPriceSettings();
        }

        return $this->priceSettings;
    }

    /**
     * @return PriceSettingsService
     */
    private function getPriceService(): PriceSettingsService
    {
        return ServiceRegister::getService(PriceSettingsService::class);
    }
}
