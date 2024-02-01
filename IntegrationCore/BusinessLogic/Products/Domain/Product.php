<?php

/** @noinspection DuplicatedCode */

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain;

/**
 * Class Product
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain
 */
class Product
{
    /**
     * @var string | int
     */
    protected $id;
    /**
     * @var float
     */
    protected $price;
    /**
     * @var int
     */
    protected $stock;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string | null
     */
    protected $description;
    /**
     * @var float | null
     */
    protected $purchasePrice;
    /**
     * @var float | null
     */
    protected $msrp;
    /**
     * @var string | null
     */
    protected $vatRateType;
    /**
     * @var float | null
     */
    protected $shippingCost;
    /**
     * @var string | null
     */
    protected $shippingTime;
    /**
     * @var string | null
     */
    protected $ean;
    /**
     * @var string | null
     */
    protected $manufacturerProductNumber;
    /**
     * @var string | null
     */
    protected $url;
    /**
     * @var string | null
     */
    protected $brand;
    /**
     * @var string | null
     */
    protected $size;
    /**
     * @var string | null
     */
    protected $color;
    /**
     * @var string | null
     */
    protected $mainImageUrl;
    /**
     * Up to 9 additional image URLs can be provided.
     *
     * @var string[]
     */
    protected $additionalImageUrls;
    /**
     * @var CustomAttribute[]
     */
    protected $customAttributes;
	/**
	 * @var string
	 */
    protected $categoryTrail;
    /**
     * @var boolean
     */
    protected $hasThreeLevelSync;
    /**
     * @var Variant[]
     */
    protected $variants;

    /**
     * Product constructor.
     *
     * @param int|string $id
     * @param float $price
     * @param int $stock
     * @param string $name
     * @param string|null $description
     * @param float|null $purchasePrice
     * @param float|null $msrp
     * @param string|null $vatRateType
     * @param float|null $shippingCost
     * @param string|null $shippingTime
     * @param string|null $ean
     * @param string|null $manufacturerProductNumber
     * @param string|null $url
     * @param string|null $brand
     * @param string|null $size
     * @param string|null $color
     * @param string|null $mainImageUrl
     * @param string[] $additionalImageUrls
     * @param CustomAttribute[] $customAttributes
     * @param string $categoryTrail
     * @param boolean $hasThreeLevelSync
     * @param Variant[] $variants
     */
    public function __construct(
        $id,
        $price,
        $stock,
        $name,
        $description = null,
        $purchasePrice = null,
        $msrp = null,
        $vatRateType = null,
        $shippingCost = null,
        $shippingTime = null,
        $ean = null,
        $manufacturerProductNumber = null,
        $url = null,
        $brand = null,
        $size = null,
        $color = null,
        $mainImageUrl = null,
        array $additionalImageUrls = [],
        array $customAttributes = [],
	    $categoryTrail = '',
        $hasThreeLevelSync = false,
        array $variants = []
    ) {
        $this->id = $id;
        $this->price = $price;
        $this->stock = $stock;
        $this->name = $name;
        $this->description = $description;
        $this->purchasePrice = $purchasePrice;
        $this->msrp = $msrp;
        $this->vatRateType = $vatRateType;
        $this->shippingCost = $shippingCost;
        $this->shippingTime = $shippingTime;
        $this->ean = $ean;
        $this->manufacturerProductNumber = $manufacturerProductNumber;
        $this->url = $url;
        $this->brand = $brand;
        $this->size = $size;
        $this->color = $color;
        $this->mainImageUrl = $mainImageUrl;
        $this->additionalImageUrls = $additionalImageUrls;
        $this->customAttributes = $customAttributes;
        $this->categoryTrail = $categoryTrail;
        $this->hasThreeLevelSync = $hasThreeLevelSync;
        $this->variants = $variants;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return float|null
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @return float|null
     */
    public function getMsrp()
    {
        return $this->msrp;
    }

    /**
     * @return string|null
     */
    public function getVatRateType()
    {
        return $this->vatRateType;
    }

    /**
     * @return float|null
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * @return string|null
     */
    public function getShippingTime()
    {
        return $this->shippingTime;
    }

    /**
     * @return string|null
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @return string|null
     */
    public function getManufacturerProductNumber()
    {
        return $this->manufacturerProductNumber;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return string|null
     */
    public function getMainImageUrl()
    {
        return $this->mainImageUrl;
    }

    /**
     * @return string[]
     */
    public function getAdditionalImageUrls()
    {
        return $this->additionalImageUrls;
    }

    /**
     * @return CustomAttribute[]
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @return Variant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * Adds variant to the list of variants.
     *
     * @param Variant $variant
     */
    public function addVariant(Variant $variant)
    {
        $this->variants[] = $variant;
    }

	/**
	 * @return string
	 */
	public function getCategoryTrail()
	{
		return $this->categoryTrail;
	}

    /**
     * @return boolean
     */
    public function getHasThreeLevelSync()
    {
        return $this->hasThreeLevelSync;
    }

    /**
     * @param bool $hasThreeLevelSync
     */
    public function setHasThreeLevelSync($hasThreeLevelSync)
    {
        $this->hasThreeLevelSync = $hasThreeLevelSync;
    }


}