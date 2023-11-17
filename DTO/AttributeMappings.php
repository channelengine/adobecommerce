<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class AttributeMappings
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class AttributeMappings extends DataTransferObject
{
    /**
     * @var string
     */
    private $merchantProductNumber;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $category;
    /**
     * @var string
     */
    private $shippingCost;
    /**
     * @var string
     */
    private $msrp;
    /**
     * @var string
     */
    private $purchasePrice;
    /**
     * @var string
     */
    private $shippingTime;
    /**
     * @var string
     */
    private $brand;
    /**
     * @var string
     */
    private $color;
    /**
     * @var string
     */
    private $size;
    /**
     * @var string
     */
    private $ean;

    /**
     * @param string $merchantProductNumber
     * @param string $name
     * @param string $description
     * @param string $category
     * @param string $shippingCost
     * @param string $msrp
     * @param string $purchasePrice
     * @param string $shippingTime
     * @param string $brand
     * @param string $color
     * @param string $size
     * @param string $ean
     */
    public function __construct(
        string $merchantProductNumber,
        string $name,
        string $description,
        string $category,
        string $shippingCost,
        string $msrp,
        string $purchasePrice,
        string $shippingTime,
        string $brand,
        string $color,
        string $size,
        string $ean
    ) {
        $this->merchantProductNumber = $merchantProductNumber;
        $this->name = $name;
        $this->description = $description;
        $this->category = $category;
        $this->shippingCost = $shippingCost;
        $this->msrp = $msrp;
        $this->purchasePrice = $purchasePrice;
        $this->shippingTime = $shippingTime;
        $this->brand = $brand;
        $this->color = $color;
        $this->size = $size;
        $this->ean = $ean;
    }

    /**
     * @return string
     */
    public function getMerchantProductNumber(): string
    {
        return $this->merchantProductNumber;
    }

    /**
     * @param string $merchantProductNumber
     */
    public function setMerchantProductNumber(string $merchantProductNumber): void
    {
        $this->merchantProductNumber = $merchantProductNumber;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getShippingCost(): string
    {
        return $this->shippingCost;
    }

    /**
     * @return string
     */
    public function getMsrp(): string
    {
        return $this->msrp;
    }

    /**
     * @return string
     */
    public function getPurchasePrice(): string
    {
        return $this->purchasePrice;
    }

    /**
     * @return string
     */
    public function getShippingTime(): string
    {
        return $this->shippingTime;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'merchantProductNumber' => $this->merchantProductNumber,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'shippingCost' => $this->shippingCost,
            'msrp' => $this->msrp,
            'purchasePrice' => $this->purchasePrice,
            'shippingTime' => $this->shippingTime,
            'brand' => $this->brand,
            'color' => $this->color,
            'size' => $this->size,
            'ean' => $this->ean,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): AttributeMappings
    {
        return new self(
            static::getDataValue($data, 'merchantProductNumber'),
            static::getDataValue($data, 'name'),
            static::getDataValue($data, 'description'),
            static::getDataValue($data, 'category'),
            static::getDataValue($data, 'shippingCost'),
            static::getDataValue($data, 'msrp'),
            static::getDataValue($data, 'purchasePrice'),
            static::getDataValue($data, 'shippingTime'),
            static::getDataValue($data, 'brand'),
            static::getDataValue($data, 'color'),
            static::getDataValue($data, 'size'),
            static::getDataValue($data, 'ean')
        );
    }
}
