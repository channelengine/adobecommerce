<?php

namespace ChannelEngine\ChannelEngineIntegration\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class AttributeMappingsTypes
 *
 * @package ChannelEngine\ChannelEngineIntegration\DTO
 */
class AttributeMappingsTypes extends DataTransferObject
{
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
     * @param string $name
     * @param string $description
     * @param string $category
     * @param string $shippingTime
     * @param string $brand
     * @param string $color
     * @param string $size
     * @param string $ean
     */
    public function __construct(
        string $name,
        string $description,
        string $category,
        string $shippingTime,
        string $brand,
        string $color,
        string $size,
        string $ean
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->category = $category;
        $this->shippingTime = $shippingTime;
        $this->brand = $brand;
        $this->color = $color;
        $this->size = $size;
        $this->ean = $ean;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getShippingTime(): string
    {
        return $this->shippingTime;
    }

    /**
     * @param string $shippingTime
     */
    public function setShippingTime(string $shippingTime): void
    {
        $this->shippingTime = $shippingTime;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @param string $ean
     */
    public function setEan(string $ean): void
    {
        $this->ean = $ean;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
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
    public static function fromArray(array $data): AttributeMappingsTypes
    {
        return new self(
            static::getDataValue($data, 'name'),
            static::getDataValue($data, 'description'),
            static::getDataValue($data, 'category'),
            static::getDataValue($data, 'shippingTime'),
            static::getDataValue($data, 'brand'),
            static::getDataValue($data, 'color'),
            static::getDataValue($data, 'size'),
            static::getDataValue($data, 'ean')
        );
    }
}
