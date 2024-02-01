<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\Transformer;

/**
 * Class Product
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO
 */
class Product extends DataTransferObject
{
    const GRANDPARENT_PREFIX = 'CEG-';
    const PARENT_PREFIX = 'CE-';

    /**
     * @var string | null
     */
    protected $merchantProductNo;
    /**
     * @var string | null
     */
    protected $parentMerchantProductNo;
    /**
     * @var string | null
     */
    protected $parentMerchantProductNo2;
    /**
     * @var ExtraData[]
     */
    protected $extraData;
    /**
     * @var string | null
     */
    protected $name;
    /**
     * @var string | null
     */
    protected $description;
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
    protected $ean;
    /**
     * @var string
     */
    protected $manufacturerProductNumber;
    /**
     * @var int
     */
    protected $stock;
    /**
     * @var float
     */
    protected $price;
    /**
     * @var float | null
     */
    protected $msrp;
    /**
     * @var float | null
     */
    protected $purchasePrice;
    /**
     * @var string One of [ STANDARD, REDUCED, SUPER_REDUCED, EXEMPT ]
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
    protected $url;
    /**
     * @var string | null
     */
    protected $imageUrl;
    /**
     * @var string | null
     */
    protected $extraImageUrl1;
    /**
     * @var string | null
     */
    protected $extraImageUrl2;
    /**
     * @var string | null
     */
    protected $extraImageUrl3;
    /**
     * @var string | null
     */
    protected $extraImageUrl4;
    /**
     * @var string | null
     */
    protected $extraImageUrl5;
    /**
     * @var string | null
     */
    protected $extraImageUrl6;
    /**
     * @var string | null
     */
    protected $extraImageUrl7;
    /**
     * @var string | null
     */
    protected $extraImageUrl8;
    /**
     * @var string | null
     */
    protected $extraImageUrl9;
    /**
     * @var string | null
     */
    protected $categoryTrail;

    /**
     * @return string|null
     */
    public function getMerchantProductNo()
    {
        return $this->merchantProductNo;
    }

    /**
     * @param string|null $merchantProductNo
     */
    public function setMerchantProductNo($merchantProductNo)
    {
        $this->merchantProductNo = $merchantProductNo;
    }

    /**
     * @return string|null
     */
    public function getParentMerchantProductNo()
    {
        return $this->parentMerchantProductNo;
    }

    /**
     * @param string|null $parentMerchantProductNo
     */
    public function setParentMerchantProductNo($parentMerchantProductNo)
    {
        $this->parentMerchantProductNo = $parentMerchantProductNo;
    }

    /**
     * @return string|null
     */
    public function getParentMerchantProductNo2()
    {
        return $this->parentMerchantProductNo2;
    }

    /**
     * @param string|null $parentMerchantProductNo2
     */
    public function setParentMerchantProductNo2($parentMerchantProductNo2)
    {
        $this->parentMerchantProductNo2 = $parentMerchantProductNo2;
    }

    /**
     * @return ExtraData[]
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param ExtraData[] $extraData
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string|null $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string|null $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string|null
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param string|null $ean
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    /**
     * @return string
     */
    public function getManufacturerProductNumber()
    {
        return $this->manufacturerProductNumber;
    }

    /**
     * @param string $manufacturerProductNumber
     */
    public function setManufacturerProductNumber($manufacturerProductNumber)
    {
        $this->manufacturerProductNumber = $manufacturerProductNumber;
    }

    /**
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float|null
     */
    public function getMsrp()
    {
        return $this->msrp;
    }

    /**
     * @param float|null $msrp
     */
    public function setMsrp($msrp)
    {
        $this->msrp = $msrp;
    }

    /**
     * @return float|null
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float|null $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return string
     */
    public function getVatRateType()
    {
        return $this->vatRateType;
    }

    /**
     * @param string $vatRateType
     */
    public function setVatRateType($vatRateType)
    {
        $this->vatRateType = $vatRateType;
    }

    /**
     * @return float|null
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * @param float|null $shippingCost
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;
    }

    /**
     * @return string|null
     */
    public function getShippingTime()
    {
        return $this->shippingTime;
    }

    /**
     * @param string|null $shippingTime
     */
    public function setShippingTime($shippingTime)
    {
        $this->shippingTime = $shippingTime;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl1()
    {
        return $this->extraImageUrl1;
    }

    /**
     * @param string|null $extraImageUrl1
     */
    public function setExtraImageUrl1($extraImageUrl1)
    {
        $this->extraImageUrl1 = $extraImageUrl1;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl2()
    {
        return $this->extraImageUrl2;
    }

    /**
     * @param string|null $extraImageUrl2
     */
    public function setExtraImageUrl2($extraImageUrl2)
    {
        $this->extraImageUrl2 = $extraImageUrl2;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl3()
    {
        return $this->extraImageUrl3;
    }

    /**
     * @param string|null $extraImageUrl3
     */
    public function setExtraImageUrl3($extraImageUrl3)
    {
        $this->extraImageUrl3 = $extraImageUrl3;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl4()
    {
        return $this->extraImageUrl4;
    }

    /**
     * @param string|null $extraImageUrl4
     */
    public function setExtraImageUrl4($extraImageUrl4)
    {
        $this->extraImageUrl4 = $extraImageUrl4;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl5()
    {
        return $this->extraImageUrl5;
    }

    /**
     * @param string|null $extraImageUrl5
     */
    public function setExtraImageUrl5($extraImageUrl5)
    {
        $this->extraImageUrl5 = $extraImageUrl5;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl6()
    {
        return $this->extraImageUrl6;
    }

    /**
     * @param string|null $extraImageUrl6
     */
    public function setExtraImageUrl6($extraImageUrl6)
    {
        $this->extraImageUrl6 = $extraImageUrl6;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl7()
    {
        return $this->extraImageUrl7;
    }

    /**
     * @param string|null $extraImageUrl7
     */
    public function setExtraImageUrl7($extraImageUrl7)
    {
        $this->extraImageUrl7 = $extraImageUrl7;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl8()
    {
        return $this->extraImageUrl8;
    }

    /**
     * @param string|null $extraImageUrl8
     */
    public function setExtraImageUrl8($extraImageUrl8)
    {
        $this->extraImageUrl8 = $extraImageUrl8;
    }

    /**
     * @return string|null
     */
    public function getExtraImageUrl9()
    {
        return $this->extraImageUrl9;
    }

    /**
     * @param string|null $extraImageUrl9
     */
    public function setExtraImageUrl9($extraImageUrl9)
    {
        $this->extraImageUrl9 = $extraImageUrl9;
    }

    /**
     * @return string|null
     */
    public function getCategoryTrail()
    {
        return $this->categoryTrail;
    }

    /**
     * @param string|null $categoryTrail
     */
    public function setCategoryTrail($categoryTrail)
    {
        $this->categoryTrail = $categoryTrail;
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $entity = new static();
        $entity->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo', null));
        $entity->setParentMerchantProductNo(static::getDataValue($data, 'ParentMerchantProductNo', null));
        $entity->setParentMerchantProductNo2(static::getDataValue($data, 'ParentMerchantProductNo2', null));
        $entity->setExtraData(ExtraData::fromBatch(static::getDataValue($data, 'ExtraData', [])));
        $entity->setName(static::getDataValue($data, 'Name', null));
        $entity->setDescription(static::getDataValue($data, 'Description', null));
        $entity->setBrand(static::getDataValue($data, 'Brand', null));
        $entity->setSize(static::getDataValue($data, 'Size', null));
        $entity->setColor(static::getDataValue($data, 'Color', null));
        $entity->setEan(static::getDataValue($data, 'Ean', null));
        $entity->setManufacturerProductNumber(static::getDataValue($data, 'ManufacturerProductNumber', null));
        $entity->setStock(static::getDataValue($data, 'Stock', 0));
        $entity->setPrice(static::getDataValue($data, 'Price', 0.0));
        $entity->setMsrp(static::getDataValue($data, 'MSRP', null));
        $entity->setPurchasePrice(static::getDataValue($data, 'PurchasePrice', null));
        $entity->setVatRateType(static::getDataValue($data, 'VatRateType'));
        $entity->setShippingCost(static::getDataValue($data, 'ShippingCost', null));
        $entity->setShippingTime(static::getDataValue($data, 'ShippingTime', null));
        $entity->setUrl(static::getDataValue($data, 'Url', null));
        $entity->setImageUrl(static::getDataValue($data, 'ImageUrl', null));
        $entity->setExtraImageUrl1(static::getDataValue($data, 'ExtraImageUrl1', null));
        $entity->setExtraImageUrl2(static::getDataValue($data, 'ExtraImageUrl2', null));
        $entity->setExtraImageUrl3(static::getDataValue($data, 'ExtraImageUrl3', null));
        $entity->setExtraImageUrl4(static::getDataValue($data, 'ExtraImageUrl4', null));
        $entity->setExtraImageUrl5(static::getDataValue($data, 'ExtraImageUrl5', null));
        $entity->setExtraImageUrl6(static::getDataValue($data, 'ExtraImageUrl6', null));
        $entity->setExtraImageUrl7(static::getDataValue($data, 'ExtraImageUrl7', null));
        $entity->setExtraImageUrl8(static::getDataValue($data, 'ExtraImageUrl8', null));
        $entity->setExtraImageUrl9(static::getDataValue($data, 'ExtraImageUrl9', null));
        $entity->setCategoryTrail(static::getDataValue($data, 'CategoryTrail', null));

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            "MerchantProductNo" => $this->getMerchantProductNo(),
            "ParentMerchantProductNo" => $this->getParentMerchantProductNo(),
            "ParentMerchantProductNo2" => $this->getParentMerchantProductNo2(),
            "ExtraData" => Transformer::batchTransform($this->getExtraData()),
            "Name" => $this->getName(),
            "Description" => $this->getDescription(),
            "Brand" => $this->getBrand(),
            "Size" => $this->getSize(),
            "Color" => $this->getColor(),
            "Ean" => $this->getEan(),
            "ManufacturerProductNumber" => $this->getManufacturerProductNumber(),
            "Stock" => $this->getStock(),
            "Price" => $this->getPrice(),
            "MSRP" => $this->getMsrp(),
            "PurchasePrice" => $this->getPurchasePrice(),
            "VatRateType" => $this->getVatRateType(),
            "ShippingCost" => $this->getShippingCost(),
            "ShippingTime" => $this->getShippingTime(),
            "Url" => $this->getUrl(),
            "ImageUrl" => $this->getImageUrl(),
            "ExtraImageUrl1" => $this->getExtraImageUrl1(),
            "ExtraImageUrl2" => $this->getExtraImageUrl2(),
            "ExtraImageUrl3" => $this->getExtraImageUrl3(),
            "ExtraImageUrl4" => $this->getExtraImageUrl4(),
            "ExtraImageUrl5" => $this->getExtraImageUrl5(),
            "ExtraImageUrl6" => $this->getExtraImageUrl6(),
            "ExtraImageUrl7" => $this->getExtraImageUrl7(),
            "ExtraImageUrl8" => $this->getExtraImageUrl8(),
            "ExtraImageUrl9" => $this->getExtraImageUrl9(),
            "CategoryTrail" => $this->getCategoryTrail(),
        ];
    }
}
