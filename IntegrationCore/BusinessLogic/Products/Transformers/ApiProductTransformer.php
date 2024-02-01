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
     * @param Product $product
     *
     * @return array
     */
    public static function transformSimpleProductToThreeLevel(Product $product)
    {
        $grandParent = static::transform($product);
        $grandParent->setMerchantProductNo(APIProduct::GRANDPARENT_PREFIX . $grandParent->getMerchantProductNo());
        $grandParent->setName(APIProduct::GRANDPARENT_PREFIX . $grandParent->getName());

        $parent = static::transform($product);
        $parent->setMerchantProductNo(APIProduct::PARENT_PREFIX . $parent->getMerchantProductNo());
        $parent->setName(APIProduct::PARENT_PREFIX . $parent->getName());
        $parent->setParentMerchantProductNo2($grandParent->getMerchantProductNo());

        $child = static::transform($product);
        $child->setParentMerchantProductNo($parent->getMerchantProductNo());

        return [$grandParent, $parent, $child];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public static function transformProductWithChildrenToThreeLevel(Product $product)
    {
        $grandParent = static::transform($product);
        $transformedProducts = [$grandParent];
        $listOfVirtualNumbers = [];
        foreach ($product->getVariants() as $variant) {
            $parentProductNo = $product->getHasThreeLevelSync() ?
                APIProduct::PARENT_PREFIX . $product->getId() . '-' . $variant->getThreeLevelSyncAttributeValue() :
                APIProduct::PARENT_PREFIX . $variant->getId();

            $parentCreated = false;
            if (!in_array($parentProductNo, $listOfVirtualNumbers)) {
                $parent = static::transform($product);
                $parentName = $product->getHasThreeLevelSync() ?
                    APIProduct::PARENT_PREFIX . $parent->getName() . '-' . $variant->getThreeLevelSyncAttributeValue() :
                    APIProduct::PARENT_PREFIX . $variant->getName();
                $parent->setMerchantProductNo($parentProductNo);
                $parent->setName($parentName);
                $parent->setParentMerchantProductNo2($grandParent->getMerchantProductNo());
                $transformedProducts[] = $parent;
                $listOfVirtualNumbers[] = $parentProductNo;
                $parentCreated = true;
            }

            $child = static::transformVariant($variant);
            $child->setParentMerchantProductNo($parentProductNo);

            $transformedProducts[] = $child;

            if ($parentCreated) {
                $index = count($transformedProducts) - 2;
                $transformedProducts[$index]->setImageUrl($child->getImageUrl());
                $transformedProducts[$index]->setExtraImageUrl1($child->getExtraImageUrl1());
                $transformedProducts[$index]->setExtraImageUrl2($child->getExtraImageUrl2());
                $transformedProducts[$index]->setExtraImageUrl3($child->getExtraImageUrl3());
                $transformedProducts[$index]->setExtraImageUrl4($child->getExtraImageUrl4());
                $transformedProducts[$index]->setExtraImageUrl5($child->getExtraImageUrl5());
                $transformedProducts[$index]->setExtraImageUrl6($child->getExtraImageUrl6());
                $transformedProducts[$index]->setExtraImageUrl7($child->getExtraImageUrl7());
                $transformedProducts[$index]->setExtraImageUrl8($child->getExtraImageUrl8());
                $transformedProducts[$index]->setExtraImageUrl9($child->getExtraImageUrl9());
            }
        }

        return $transformedProducts;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public static function transformProductToTwoLevel(Product $product)
    {
        $transformedProducts = [static::transform($product)];
        foreach ($product->getVariants() as $variant) {
            $transformedProducts[] = static::transformVariant($variant);
        }

        return $transformedProducts;
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
