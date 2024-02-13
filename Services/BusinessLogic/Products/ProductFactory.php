<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\Api\StockServiceFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappingsTypes;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\CustomAttribute;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Product;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Variant;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsTypesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductFactory
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class ProductFactory
{
    public const SPECIAL_CHARS = ['&bull;'];
    /**
     * @var AttributeMappings
     */
    private $attributeMappings;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var PriceService
     */
    private $priceService;
    /**
     * @var AttributesService
     */
    private $attributesService;
    /**
     * @var ExtraFieldsService
     */
    private $extraFieldsService;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ProductResource
     */
    private $productResource;
    /**
     * @var StockServiceFactoryInterface
     */
    private $stockServiceFactory;

    /**
     * @param ProductRepository $productRepository
     * @param PriceService $priceService
     * @param AttributesService $attributesService
     * @param ExtraFieldsService $extraFieldsService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CategoryService $categoryService
     * @param StoreManagerInterface $storeManager
     * @param ProductResource $productResource
     * @param StockServiceFactoryInterface $stockServiceFactory
     */
    public function __construct(
        ProductRepository            $productRepository,
        PriceService                 $priceService,
        AttributesService            $attributesService,
        ExtraFieldsService           $extraFieldsService,
        SearchCriteriaBuilder        $searchCriteriaBuilder,
        CategoryService              $categoryService,
        StoreManagerInterface        $storeManager,
        ProductResource              $productResource,
        StockServiceFactoryInterface $stockServiceFactory
    ) {
        $this->productRepository = $productRepository;
        $this->priceService = $priceService;
        $this->attributesService = $attributesService;
        $this->extraFieldsService = $extraFieldsService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryService = $categoryService;
        $this->storeManager = $storeManager;
        $this->productResource = $productResource;
        $this->stockServiceFactory = $stockServiceFactory;
    }

    /**
     * Retrieves product.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return Product
     *
     * @throws QueryFilterInvalidParamException
     * @throws LocalizedException
     */
    public function getProduct(\Magento\Catalog\Model\Product $product): Product
    {
        $attributeMappings = $this->getAttributeMappings();
        $attributeMappingsTypes = $this->getAttributeMappingsTypes();
        $productAttributes = $product->getAttributes();
        $customAttributes = $product->getCustomAttributes();
        $images = $product->getMediaGalleryImages();
        $imageUrl = $images->getFirstItem()->getUrl();
        $productImages = [];
        $variants = [];

        foreach ($images as $image) {
            $productImages[] = $image->getUrl();
        }
        array_shift($productImages);

        $extraImages = array_slice($productImages, 9);
        $extraDataImages = [];
        $imageNumber = 11;

        foreach ($extraImages as $image) {
            $extraDataImages[] = new CustomAttribute(
                'ExtraImageUrl' . $imageNumber,
                $image,
                CustomAttribute::TYPE_IMG_URL
            );

            $imageNumber++;

            if (count($extraDataImages) === 10) {
                break;
            }
        }

        /** @noinspection NestedTernaryOperatorInspection */
        $ceProduct = new Product(
            $attributeMappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID ?
                $product->getId() : $product->getSku(),
            $this->priceService->getProductPrice($product),
            $this->getStock($product),
            $attributeMappings->getName() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getName(),
                    $attributeMappingsTypes->getName(),
                    $product
                ),
            $attributeMappings->getDescription() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getDescription(),
                    $attributeMappingsTypes->getDescription(),
                    $product
                ),
            $this->attributesService->getAttribute(
                $productAttributes,
                $customAttributes,
                $attributeMappings->getPurchasePrice(),
                '',
                $product
            ),
            $this->attributesService->getAttribute(
                $productAttributes,
                $customAttributes,
                $attributeMappings->getMsrp(),
                '',
                $product
            ),
            'STANDARD',
            $this->attributesService->getAttribute(
                $productAttributes,
                $customAttributes,
                $attributeMappings->getShippingCost(),
                '',
                $product
            ),
            $attributeMappings->getShippingTime() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getShippingTime(),
                    $attributeMappingsTypes->getShippingTime(),
                    $product
                ),
            $attributeMappings->getEan() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getEan(),
                    $attributeMappingsTypes->getEan(),
                    $product
                ),
            $attributeMappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID ?
                $product->getSku() : (string)$product->getId(),
            $product->getProductUrl(),
            $attributeMappings->getBrand() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getBrand(),
                    $attributeMappingsTypes->getBrand(),
                    $product
                ),
            $attributeMappings->getSize() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getSize(),
                    $attributeMappingsTypes->getSize(),
                    $product
                ),
            $attributeMappings->getColor() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getColor(),
                    $attributeMappingsTypes->getColor(),
                    $product
                ),
            $imageUrl,
            $productImages ?? [],
            array_merge(
                $this->extraFieldsService->getExtraFields($productAttributes, $customAttributes, $this->getData($product)),
                $extraDataImages
            ),
            $attributeMappings->getCategory() === 'category_ids' ? $this->categoryService->getCategoryTrail($product->getCategoryIds()) :
                ($attributeMappings->getCategory() === 'name' ?
                    $product->getName() :
                    $this->attributesService->getAttribute(
                        $productAttributes,
                        $customAttributes,
                        $attributeMappings->getCategory(),
                        $attributeMappingsTypes->getCategory(),
                        $product
                    )
                )
        );

        switch ($product->getTypeId()) {
            case 'configurable':
                $variants = $product->getTypeInstance()->getUsedProducts($product);
                break;
            case 'bundle':
                $variantIds = $product->getTypeInstance()->getChildrenIds($product->getId());
                $this->searchCriteriaBuilder->addFilter('entity_id', $variantIds, 'in');
                $criteria = $this->searchCriteriaBuilder->create();
                $variants = $this->productRepository->getList($criteria)->getItems();
                break;
            case 'grouped':
                $variants = $product->getTypeInstance()->getAssociatedProducts($product);
                break;
        }

        $threeLevelSyncSettings = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
        $threeLevelSyncAttribute = $threeLevelSyncSettings ? $threeLevelSyncSettings->getSyncAttribute() : '';
        $hasThreeLevelSync = $threeLevelSyncAttribute !== '' && array_key_exists($threeLevelSyncAttribute, $productAttributes);
        $ceProduct->setHasThreeLevelSync($hasThreeLevelSync);

        foreach ($variants as $variant) {
            $objectManager = ObjectManager::getInstance();
            $variant = $objectManager->get('Magento\Catalog\Model\Product')->load($variant->getId());

            $domainVariant = $this->getVariant($variant, $ceProduct);

            if ($ceProduct->getHasThreeLevelSync() && $variant->getData($threeLevelSyncAttribute)) {
                $variantAttributeValue = $variant->getAttributeText($threeLevelSyncAttribute);
                $domainVariant->setThreeLevelSyncAttributeValue($variantAttributeValue);
            }

            $ceProduct->addVariant($domainVariant);
        }

        return $ceProduct;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getData(\Magento\Catalog\Model\Product $product): array
    {
        $this->productResource->load($product, $product->getId());
        $attributes = $product->getAttributes();

        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributeValue = $product->getData($attributeCode);

            $data[$attributeCode] = $attributeValue;
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param Product $parentProduct
     *
     * @return Variant
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws QueryFilterInvalidParamException
     */
    private function getVariant(\Magento\Catalog\Model\Product $product, Product $parentProduct): Variant
    {
        $attributeMappings = $this->getAttributeMappings();
        $attributeMappingsTypes = $this->getAttributeMappingsTypes();
        $productAttributes = $product->getAttributes();
        $customAttributes = $product->getCustomAttributes();
        $productImages = [];

        $store = $this->storeManager->getStore();
        $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $images = $product->getMediaGalleryEntries();
        foreach ($images as $image) {
            $productImages[] = $baseUrl . 'catalog/product' . $image->getFile();
        }

        $imageUrl = !empty($images) ? $productImages[0] : null;
        array_shift($productImages);

        if ($imageUrl) {
            $position = strstr($imageUrl, '/w/');
            $imageUrl = str_replace($position, $product->getImage(), $imageUrl);
        }

        $extraImages = array_slice($productImages, 9);
        $extraDataImages = [];
        $imageNumber = 11;

        foreach ($extraImages as $image) {
            $extraDataImages[] = new CustomAttribute(
                'ExtraImageUrl' . $imageNumber,
                $image,
                CustomAttribute::TYPE_IMG_URL
            );

            $imageNumber++;

            if (count($extraDataImages) === 10) {
                break;
            }
        }

        $price = $this->priceService->getProductPrice($product);
        $stock = $this->getStock($product);
        $purchasePrice = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getPurchasePrice(),
            '',
            $product
        );
        $msrp = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getMsrp(),
            '',
            $product
        );
        $shippingCost = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getShippingCost(),
            '',
            $product
        );
        $shippingTime = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getShippingTime(),
            $attributeMappingsTypes->getShippingTime(),
            $product
        );
        $ean = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getEan(),
            $attributeMappingsTypes->getEan(),
            $product
        );
        $brand = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getBrand(),
            $attributeMappingsTypes->getBrand(),
            $product
        );
        $size = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getSize(),
            $attributeMappingsTypes->getSize(),
            $product
        );
        $color = $this->attributesService->getAttribute(
            $productAttributes,
            $customAttributes,
            $attributeMappings->getColor(),
            $attributeMappingsTypes->getColor(),
            $product
        );
        $extraFields = $this->extraFieldsService->getExtraFields($productAttributes, $customAttributes, $product->getData());
        $name = $attributeMappings->getName() === 'name' ?
            $product->getName() :
            $this->attributesService->getAttribute(
                $productAttributes,
                $customAttributes,
                $attributeMappings->getName(),
                $attributeMappingsTypes->getName(),
                $product
            );
        $description = $attributeMappings->getDescription() === 'name' ?
            $product->getName() :
            $this->attributesService->getAttribute(
                $productAttributes,
                $customAttributes,
                $attributeMappings->getDescription(),
                $attributeMappingsTypes->getDescription(),
                $product
            );
        /** @noinspection NestedTernaryOperatorInspection */
        $categoryTrail = $attributeMappings->getCategory() === 'category_ids' ? $this->categoryService->getCategoryTrail($product->getCategoryIds()) :
            ($attributeMappings->getCategory() === 'name' ?
                $product->getName() :
                $this->attributesService->getAttribute(
                    $productAttributes,
                    $customAttributes,
                    $attributeMappings->getCategory(),
                    $attributeMappingsTypes->getCategory(),
                    $product
                )
            );

        return new Variant(
            $attributeMappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID ?
                $product->getId() : $product->getSku(),
            $parentProduct,
            $price ?? $parentProduct->getPrice(),
            $stock ?? $parentProduct->getStock(),
            $name ?? $parentProduct->getName(),
            $description ?? $parentProduct->getDescription(),
            (float)($purchasePrice ?? $parentProduct->getPurchasePrice()),
            (float)($msrp ?? $parentProduct->getMsrp()),
            'STANDARD',
            (float)($shippingCost ?? $parentProduct->getShippingCost()),
            $shippingTime ?? $parentProduct->getShippingTime(),
            $ean ?? $parentProduct->getEan(),
            ($attributeMappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID ?
                $product->getSku() : (string)$product->getId()) ?? $parentProduct->getManufacturerProductNumber(),
            $product->getProductUrl() ?? $parentProduct->getUrl(),
            $brand ?? $parentProduct->getBrand(),
            $size ?? $parentProduct->getSize(),
            $color ?? $parentProduct->getColor(),
            $imageUrl ?? $parentProduct->getMainImageUrl(),
            $productImages ?? $parentProduct->getAdditionalImageUrls(),
            array_merge(
                $extraFields ?? $parentProduct->getCustomAttributes(),
                $extraDataImages,
                [
                    new CustomAttribute(
                        'ConfigurableParentProductSku',
                        $parentProduct->getId(),
                        CustomAttribute::TYPE_TEXT
                    ),
                ]
            ),
            $categoryTrail ?? $parentProduct->getCategoryTrail()
        );
    }

    /**
     * @param ProductInterface $product
     * @return int
     */
    private function getStock(ProductInterface $product): int
    {
        return $this->stockServiceFactory->create()->getStock($product);
    }

    /**
     * @return AttributeMappings
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getAttributeMappings(): AttributeMappings
    {
        if ($this->attributeMappings === null) {
            $this->attributeMappings = $this->getAttributeMappingsService()->getAttributeMappings();
        }

        return $this->attributeMappings;
    }

    /**
     * @return AttributeMappingsTypes
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getAttributeMappingsTypes(): AttributeMappingsTypes
    {
        return $this->getAttributeMappingsTypesService()->getAttributeMappings();
    }

    /**
     * @return AttributeMappingsService
     */
    private function getAttributeMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }

    /**
     * @return ThreeLevelSyncSettingsService
     */
    private function getThreeLevelSyncSettingsService(): ThreeLevelSyncSettingsService
    {
        return ServiceRegister::getService(ThreeLevelSyncSettingsService::class);
    }

    /**
     * @return AttributeMappingsTypesService
     */
    private function getAttributeMappingsTypesService(): AttributeMappingsTypesService
    {
        return ServiceRegister::getService(AttributeMappingsTypesService::class);
    }
}
