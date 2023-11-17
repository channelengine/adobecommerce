<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Data\TimestampAware;
use DateTime;

/**
 * Class OrderLine
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class OrderLine extends TimestampAware
{
    /**
     * @var string
     */
    private $status;
    /**
     * @var bool
     */
    private $isFulfillmentByMarketplace;
    /**
     * @var string | null
     */
    private $gtin;
    /**
     * @var string | null
     */
    private $description;
    /**
     * @var StockLocation
     */
    private $stockLocation;
    /**
     * @var float | null
     */
    private $unitVat;
    /**
     * @var float | null
     */
    private $lineTotalInclVat;
    /**
     * @var float | null
     */
    private $lineVat;
    /**
     * @var float | null
     */
    private $originalUnitPriceInclVat;
    /**
     * @var float | null
     */
    private $originalUnitVat;
    /**
     * @var float | null
     */
    private $originalLineTotalInclVat;
    /**
     * @var float | null
     */
    private $originalLineVat;
    /**
     * @var float
     */
    private $originalFeeFixed;
    /**
     * @var string | null
     */
    private $bundleProductMerchantProductNo;
    /**
     * @var string | null
     */
    private $jurisCode;
    /**
     * @var string | null
     */
    private $jurisName;
    /**
     * @var float
     */
    private $vatRate;
    /**
     * @var ExtraData[]
     */
    private $extraData;
    /**
     * @var string
     */
    private $channelProductNo;
    /**
     * @var string | null
     */
    private $merchantProductNo;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var int
     */
    private $cancellationRequestedQuantity;
    /**
     * @var float
     */
    private $unitPriceInclVat;
    /**
     * @var float
     */
    private $feeFixed;
    /**
     * @var float
     */
    private $feeRate;
    /**
     * @var string
     */
    private $condition;
    /**
     * @var DateTime | null
     */
    private $expectedDeliveryDate;

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isFulfillmentByMarketplace()
    {
        return $this->isFulfillmentByMarketplace;
    }

    /**
     * @param bool $isFulfillmentByMarketplace
     */
    public function setIsFulfillmentByMarketplace($isFulfillmentByMarketplace)
    {
        $this->isFulfillmentByMarketplace = $isFulfillmentByMarketplace;
    }

    /**
     * @return string|null
     */
    public function getGtin()
    {
        return $this->gtin;
    }

    /**
     * @param string|null $gtin
     */
    public function setGtin($gtin)
    {
        $this->gtin = $gtin;
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
     * @return StockLocation
     */
    public function getStockLocation()
    {
        return $this->stockLocation;
    }

    /**
     * @param StockLocation $stockLocation
     */
    public function setStockLocation($stockLocation)
    {
        $this->stockLocation = $stockLocation;
    }

    /**
     * @return float|null
     */
    public function getUnitVat()
    {
        return $this->unitVat;
    }

    /**
     * @param float|null $unitVat
     */
    public function setUnitVat($unitVat)
    {
        $this->unitVat = $unitVat;
    }

    /**
     * @return float|null
     */
    public function getLineTotalInclVat()
    {
        return $this->lineTotalInclVat;
    }

    /**
     * @param float|null $lineTotalInclVat
     */
    public function setLineTotalInclVat($lineTotalInclVat)
    {
        $this->lineTotalInclVat = $lineTotalInclVat;
    }

    /**
     * @return float|null
     */
    public function getLineVat()
    {
        return $this->lineVat;
    }

    /**
     * @param float|null $lineVat
     */
    public function setLineVat($lineVat)
    {
        $this->lineVat = $lineVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalUnitPriceInclVat()
    {
        return $this->originalUnitPriceInclVat;
    }

    /**
     * @param float|null $originalUnitPriceInclVat
     */
    public function setOriginalUnitPriceInclVat($originalUnitPriceInclVat)
    {
        $this->originalUnitPriceInclVat = $originalUnitPriceInclVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalUnitVat()
    {
        return $this->originalUnitVat;
    }

    /**
     * @param float|null $originalUnitVat
     */
    public function setOriginalUnitVat($originalUnitVat)
    {
        $this->originalUnitVat = $originalUnitVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalLineTotalInclVat()
    {
        return $this->originalLineTotalInclVat;
    }

    /**
     * @param float|null $originalLineTotalInclVat
     */
    public function setOriginalLineTotalInclVat($originalLineTotalInclVat)
    {
        $this->originalLineTotalInclVat = $originalLineTotalInclVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalLineVat()
    {
        return $this->originalLineVat;
    }

    /**
     * @param float|null $originalLineVat
     */
    public function setOriginalLineVat($originalLineVat)
    {
        $this->originalLineVat = $originalLineVat;
    }

    /**
     * @return float
     */
    public function getOriginalFeeFixed()
    {
        return $this->originalFeeFixed;
    }

    /**
     * @param float $originalFeeFixed
     */
    public function setOriginalFeeFixed($originalFeeFixed)
    {
        $this->originalFeeFixed = $originalFeeFixed;
    }

    /**
     * @return string|null
     */
    public function getBundleProductMerchantProductNo()
    {
        return $this->bundleProductMerchantProductNo;
    }

    /**
     * @param string|null $bundleProductMerchantProductNo
     */
    public function setBundleProductMerchantProductNo($bundleProductMerchantProductNo)
    {
        $this->bundleProductMerchantProductNo = $bundleProductMerchantProductNo;
    }

    /**
     * @return string|null
     */
    public function getJurisCode()
    {
        return $this->jurisCode;
    }

    /**
     * @param string|null $jurisCode
     */
    public function setJurisCode($jurisCode)
    {
        $this->jurisCode = $jurisCode;
    }

    /**
     * @return string|null
     */
    public function getJurisName()
    {
        return $this->jurisName;
    }

    /**
     * @param string|null $jurisName
     */
    public function setJurisName($jurisName)
    {
        $this->jurisName = $jurisName;
    }

    /**
     * @return float
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * @param float $vatRate
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;
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
     * @return string
     */
    public function getChannelProductNo()
    {
        return $this->channelProductNo;
    }

    /**
     * @param string $channelProductNo
     */
    public function setChannelProductNo($channelProductNo)
    {
        $this->channelProductNo = $channelProductNo;
    }

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
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getCancellationRequestedQuantity()
    {
        return $this->cancellationRequestedQuantity;
    }

    /**
     * @param int $cancellationRequestedQuantity
     */
    public function setCancellationRequestedQuantity($cancellationRequestedQuantity)
    {
        $this->cancellationRequestedQuantity = $cancellationRequestedQuantity;
    }

    /**
     * @return float
     */
    public function getUnitPriceInclVat()
    {
        return $this->unitPriceInclVat;
    }

    /**
     * @param float $unitPriceInclVat
     */
    public function setUnitPriceInclVat($unitPriceInclVat)
    {
        $this->unitPriceInclVat = $unitPriceInclVat;
    }

    /**
     * @return float
     */
    public function getFeeFixed()
    {
        return $this->feeFixed;
    }

    /**
     * @param float $feeFixed
     */
    public function setFeeFixed($feeFixed)
    {
        $this->feeFixed = $feeFixed;
    }

    /**
     * @return float
     */
    public function getFeeRate()
    {
        return $this->feeRate;
    }

    /**
     * @param float $feeRate
     */
    public function setFeeRate($feeRate)
    {
        $this->feeRate = $feeRate;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return DateTime|null
     */
    public function getExpectedDeliveryDate()
    {
        return $this->expectedDeliveryDate;
    }

    /**
     * @param DateTime|null $expectedDeliveryDate
     */
    public function setExpectedDeliveryDate($expectedDeliveryDate)
    {
        $this->expectedDeliveryDate = $expectedDeliveryDate;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $extra = [];

        foreach ($this->extraData as $data) {
            $extra[] = $data->toArray();
        }

        return [
            'Status' => $this->status,
            'IsFulfillmentByMarketplace' => $this->isFulfillmentByMarketplace,
            'Gtin' => $this->gtin,
            'Description' => $this->description,
            'StockLocation' => $this->stockLocation->toArray(),
            'UnitVat' => $this->unitVat,
            'LineTotalInclVat' => $this->lineTotalInclVat,
            'LineVat' => $this->lineVat,
            'OriginalUnitPriceInclVat' => $this->originalUnitPriceInclVat,
            'OriginalUnitVat' => $this->originalUnitVat,
            'OriginalLineTotalInclVat' => $this->originalLineTotalInclVat,
            'OriginalLineVat' => $this->originalLineVat,
            'OriginalFeeFixed' => $this->originalFeeFixed,
            'BundleProductMerchantProductNo' => $this->bundleProductMerchantProductNo,
            'JurisCode' => $this->jurisCode,
            'JurisName' => $this->jurisName,
            'VatRate' => $this->vatRate,
            'ExtraData' => $extra,
            'ChannelProductNo' => $this->channelProductNo,
            'MerchantProductNo' => $this->merchantProductNo,
            'Quantity' => $this->quantity,
            'CancellationRequestedQuantity' => $this->cancellationRequestedQuantity,
            'UnitPriceInclVat' => $this->unitPriceInclVat,
            'FeeFixed' => $this->feeFixed,
            'FeeRate' => $this->feeRate,
            'Condition' => $this->condition,
            'ExpectedDeliveryDate' =>
                substr(date_format($this->expectedDeliveryDate, 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
        ];
    }

    public static function fromArray(array $data)
    {
        $line = new self();

        $line->setStatus(static::getDataValue($data, 'Status'));
        $line->setIsFulfillmentByMarketplace(static::getDataValue($data, 'IsFulfillmentByMarketplace', false));
        $line->setGtin(static::getDataValue($data, 'Gtin'));
        $line->setDescription(static::getDataValue($data, 'Description'));
        $line->setStockLocation(StockLocation::fromArray(static::getDataValue($data, 'StockLocation', [])));
        $line->setUnitVat(static::getDataValue($data, 'UnitVat', null));
        $line->setLineTotalInclVat(static::getDataValue($data, 'LineTotalInclVat', null));
        $line->setLineVat(static::getDataValue($data, 'LineVat', null));
        $line->setOriginalUnitPriceInclVat(static::getDataValue($data, 'OriginalUnitPriceInclVat', null));
        $line->setOriginalUnitVat(static::getDataValue($data, 'OriginalUnitVat', null));
        $line->setOriginalLineTotalInclVat(static::getDataValue($data, 'OriginalLineTotalInclVat', null));
        $line->setOriginalLineVat(static::getDataValue($data, 'OriginalLineVat', null));
        $line->setOriginalFeeFixed(static::getDataValue($data, 'OriginalFeeFixed', 0));
        $line->setBundleProductMerchantProductNo(static::getDataValue($data, 'BundleProductMerchantProductNo'));
        $line->setJurisCode(static::getDataValue($data, 'JurisCode'));
        $line->setJurisName(static::getDataValue($data, 'JurisName'));
        $line->setVatRate(static::getDataValue($data, 'VatRate', 0));
        $line->setExtraData(ExtraData::fromBatch(static::getDataValue($data, 'ExtraData', [])));
        $line->setChannelProductNo(static::getDataValue($data, 'ChannelProductNo'));
        $line->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
        $line->setQuantity(static::getDataValue($data, 'Quantity', 0));
        $line->setCancellationRequestedQuantity(static::getDataValue($data, 'CancellationRequestedQuantity'));
        $line->setUnitPriceInclVat(static::getDataValue($data, 'UnitPriceInclVat', 0));
        $line->setFeeFixed(static::getDataValue($data, 'FeeFixed', 0));
        $line->setFeeRate(static::getDataValue($data, 'FeeRate', 0));
        $line->setCondition(static::getDataValue($data, 'Condition'));
        $line->setExpectedDeliveryDate(static::getDate(static::getDataValue($data, 'ExpectedDeliveryDate', null)));

        return $line;
    }
}