<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Transformers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO\ExtraData;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO\Product as APIProduct;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Product;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Variant;

/**
 * Class ApiProductTransformer
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Transformers
 */
class ApiProductTransformer
{
    /**
     * Transforms product to API product.
     *
     * @param Product $product
     *
     * @return APIProduct
     */
    public static function transformDomainProduct(Product $product)
    {
        return static::transform($product);
    }

    /**
     * Transforms variant to API product.
     *
     * @param Variant $variant
     *
     * @return APIProduct
     */
    public static function transformVariant(Variant $variant)
    {
        $apiProduct = static::transform($variant);
        $apiProduct->setParentMerchantProductNo($variant->getParent()->getId());

        return $apiProduct;
    }

    /**
     * Transforms products and variants to API product.
     *
     * @param Product | Variant $product
     *
     * @return APIProduct
     */
    protected static function transform($product)
    {
        $apiProduct = new APIProduct();
        $apiProduct->setMerchantProductNo($product->getId());
        $apiProduct->setDescription($product->getDescription());
        $apiProduct->setBrand($product->getBrand());
        $apiProduct->setColor($product->getColor());
        $apiProduct->setEan($product->getEan());
        $apiProduct->setManufacturerProductNumber($product->getManufacturerProductNumber());
        $apiProduct->setMsrp($product->getMsrp());
        $apiProduct->setName($product->getName());
        $apiProduct->setPrice($product->getPrice());
        $apiProduct->setPurchasePrice($product->getPurchasePrice());
        $apiProduct->setShippingCost($product->getShippingCost());
        $apiProduct->setShippingTime($product->getShippingTime());
        $apiProduct->setSize($product->getSize());
        $apiProduct->setStock($product->getStock());
        $apiProduct->setUrl($product->getUrl());
        $apiProduct->setVatRateType($product->getVatRateType());
        $apiProduct->setImageUrl($product->getMainImageUrl());
        $apiProduct->setCategoryTrail($product->getCategoryTrail());

        $imageUrls = $product->getAdditionalImageUrls();
        if ($imageUrls) {
            $imageNumber = 1;
            $method = 'setExtraImageUrl';

            foreach ($imageUrls as $imageUrl) {
                $methodName = $method . $imageNumber;
                $apiProduct->$methodName($imageUrl);
                if ($imageNumber === 9) {
                    break;
                }
                $imageNumber++;
            }
        }

        $data = [];
        foreach ($product->getCustomAttributes() as $attribute) {
            $extraData = new ExtraData();
            $extraData->setType($attribute->getType());
            $extraData->setValue($attribute->getValue());
            $extraData->setKey($attribute->getKey());
            $extraData->setIsPublic($attribute->isPublic());

            $data[] = $extraData;
        }
        $apiProduct->setExtraData($data);

        return $apiProduct;
    }
}