<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantShipmentLineResponse
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class MerchantShipmentLineResponse extends DataTransferObject
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
     * @var string
     */
    private $gtin;
    /**
     * @var string
     */
    private $description;
    /**
     * @var StockLocation
     */
    private $stockLocation;
    /**
     * @var double
     */
    private $unitVat;
    /**
     * @var double
     */
    private $lineTotalInclVat;
    /**
     * @var double
     */
    private $lineVat;
    /**
     * @var double
     */
    private $originalUnitPriceInclVat;
    /**
     * @var double
     */
    private $originalUnitVat;
    /**
     * @var double
     */
    private $originalLineTotalInclVat;
    /**
     * @var double
     */
    private $originalLineVat;
    /**
     * @var double
     */
    private $originalFeeFixed;
    /**
     * @var string
     */
    private $bundleProductMerchantProductNo;
    /**
     * @var string
     */
    private $jurisCode;
    /**
     * @var string
     */
    private $jurisName;
    /**
     * @var double
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
     * @var string
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
     * @var double
     */
    private $unitPriceInclVat;
    /**
     * @var double
     */
    private $feeFixed;
    /**
     * @var double
     */
    private $feeRate;
    /**
     * @var string
     */
    private $condition;
    /**
     * @var string
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
     * @return string
     */
    public function getGtin()
    {
        return $this->gtin;
    }

    /**
     * @param string $gtin
     */
    public function setGtin($gtin)
    {
        $this->gtin = $gtin;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
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
     * @return float
     */
    public function getUnitVat()
    {
        return $this->unitVat;
    }

    /**
     * @param float $unitVat
     */
    public function setUnitVat($unitVat)
    {
        $this->unitVat = $unitVat;
    }

    /**
     * @return float
     */
    public function getLineTotalInclVat()
    {
        return $this->lineTotalInclVat;
    }

    /**
     * @param float $lineTotalInclVat
     */
    public function setLineTotalInclVat($lineTotalInclVat)
    {
        $this->lineTotalInclVat = $lineTotalInclVat;
    }

    /**
     * @return float
     */
    public function getLineVat()
    {
        return $this->lineVat;
    }

    /**
     * @param float $lineVat
     */
    public function setLineVat($lineVat)
    {
        $this->lineVat = $lineVat;
    }

    /**
     * @return float
     */
    public function getOriginalUnitPriceInclVat()
    {
        return $this->originalUnitPriceInclVat;
    }

    /**
     * @param float $originalUnitPriceInclVat
     */
    public function setOriginalUnitPriceInclVat($originalUnitPriceInclVat)
    {
        $this->originalUnitPriceInclVat = $originalUnitPriceInclVat;
    }

    /**
     * @return float
     */
    public function getOriginalUnitVat()
    {
        return $this->originalUnitVat;
    }

    /**
     * @param float $originalUnitVat
     */
    public function setOriginalUnitVat($originalUnitVat)
    {
        $this->originalUnitVat = $originalUnitVat;
    }

    /**
     * @return float
     */
    public function getOriginalLineTotalInclVat()
    {
        return $this->originalLineTotalInclVat;
    }

    /**
     * @param float $originalLineTotalInclVat
     */
    public function setOriginalLineTotalInclVat($originalLineTotalInclVat)
    {
        $this->originalLineTotalInclVat = $originalLineTotalInclVat;
    }

    /**
     * @return float
     */
    public function getOriginalLineVat()
    {
        return $this->originalLineVat;
    }

    /**
     * @param float $originalLineVat
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
     * @return string
     */
    public function getBundleProductMerchantProductNo()
    {
        return $this->bundleProductMerchantProductNo;
    }

    /**
     * @param string $bundleProductMerchantProductNo
     */
    public function setBundleProductMerchantProductNo($bundleProductMerchantProductNo)
    {
        $this->bundleProductMerchantProductNo = $bundleProductMerchantProductNo;
    }

    /**
     * @return string
     */
    public function getJurisCode()
    {
        return $this->jurisCode;
    }

    /**
     * @param string $jurisCode
     */
    public function setJurisCode($jurisCode)
    {
        $this->jurisCode = $jurisCode;
    }

    /**
     * @return string
     */
    public function getJurisName()
    {
        return $this->jurisName;
    }

    /**
     * @param string $jurisName
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
     * @return string
     */
    public function getMerchantProductNo()
    {
        return $this->merchantProductNo;
    }

    /**
     * @param string $merchantProductNo
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
     * @return string
     */
    public function getExpectedDeliveryDate()
    {
        return $this->expectedDeliveryDate;
    }

    /**
     * @param string $expectedDeliveryDate
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
        $extraData = [];

        foreach ($this->extraData as $data) {
            $extraData[] = $data->toArray();
        }

        return [
            'Status' => $this->status,
            'IsFulfillmentByMarketplace' => $this->isFulfillmentByMarketplace,
            'Gtin' => $this->gtin,
            'Description' => $this->description,
            'StockLocation' => $this->stockLocation ? $this->stockLocation->toArray() : null,
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
            'ExtraData' => $extraData,
            'ChannelProductNo' => $this->channelProductNo,
            'MerchantProductNo' => $this->merchantProductNo,
            'Quantity' => $this->quantity,
            'CancellationRequestedQuantity' => $this->cancellationRequestedQuantity,
            'UnitPriceInclVat' => $this->unitPriceInclVat,
            'FeeFixed' => $this->feeFixed,
            'FeeRate' => $this->feeRate,
            'Condition' => $this->condition,
            'ExpectedDeliveryDate' => $this->expectedDeliveryDate
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $shipmentLine = new self();
        $shipmentLine->setStatus(static::getDataValue($data, 'Status'));
        $shipmentLine->setIsFulfillmentByMarketplace(static::getDataValue($data, 'IsFulfillmentByMarketplace', false));
        $shipmentLine->setGtin(static::getDataValue($data, 'Gtin', null));
        $shipmentLine->setDescription(static::getDataValue($data, 'Description'));
        $shipmentLine->setStockLocation(static::getDataValue($data, 'StockLocation', []) !== [] ?
            StockLocation::fromArray(static::getDataValue($data, 'StockLocation', [])) : null);
        $shipmentLine->setUnitVat(static::getDataValue($data, 'UnitVat', 0));
        $shipmentLine->setLineTotalInclVat(static::getDataValue($data, 'LineTotalInclVat', 0));
        $shipmentLine->setLineVat(static::getDataValue($data, 'LineVat', 0));
        $shipmentLine->setOriginalUnitPriceInclVat(static::getDataValue($data, 'OriginalUnitPriceInclVat', 0));
        $shipmentLine->setOriginalUnitVat(static::getDataValue($data, 'OriginalUnitVat', 0));
        $shipmentLine->setOriginalLineTotalInclVat(static::getDataValue($data, 'OriginalLineTotalInclVat', 0));
        $shipmentLine->setOriginalLineVat(static::getDataValue($data, 'OriginalLineVat', 0));
        $shipmentLine->setOriginalFeeFixed(static::getDataValue($data, 'OriginalFeeFixed', 0));
        $shipmentLine->setBundleProductMerchantProductNo(static::getDataValue($data, 'BundleProductMerchantProductNo', null));
        $shipmentLine->setJurisCode(static::getDataValue($data, 'JurisCode', null));
        $shipmentLine->setJurisName(static::getDataValue($data, 'JurisName', null));
        $shipmentLine->setVatRate(static::getDataValue($data, 'VatRate'));
        $shipmentLine->setExtraData(ExtraData::fromBatch(static::getDataValue($data, 'ExtraData', [])));
        $shipmentLine->setChannelProductNo(static::getDataValue($data, 'ChannelProductNo'));
        $shipmentLine->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
        $shipmentLine->setQuantity(static::getDataValue($data, 'Quantity', 0));
        $shipmentLine->setCancellationRequestedQuantity(static::getDataValue($data, 'CancellationRequestedQuantity', 0));
        $shipmentLine->setUnitPriceInclVat(static::getDataValue($data, 'UnitPriceInclVat'));
        $shipmentLine->setFeeFixed(static::getDataValue($data, 'FeeFixed', 0));
        $shipmentLine->setFeeRate(static::getDataValue($data, 'FeeRate', 0));
        $shipmentLine->setCondition(static::getDataValue($data, 'Condition'));
        $shipmentLine->setExpectedDeliveryDate(static::getDataValue($data, 'ExpectedDeliveryDate'));

        return $shipmentLine;
    }
}