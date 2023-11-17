<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Data\TimestampAware;
use DateTime;

/**
 * Class LineItem
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO
 */
class LineItem extends TimestampAware
{
    /**
     * Available statuses:
     * IN_PROGRESS
     * SHIPPED
     * IN_BACKORDER
     * MANCO
     * CANCELED
     * IN_COMBI
     * CLOSED
     * NEW
     * RETURNED
     * REQUIRES_CORRECTION
     *
     * @var string
     */
    protected $status;
    /**
     * Is the order fulfilled by the marketplace.
     *
     * @var bool
     */
    protected $isFulfillmentByMarketplace;
    /**
     * GTIN.
     *
     * @var string | null
     */
    protected $gtin;
    /**
     * The product description.
     *
     * @var string | null
     */
    protected $description;
    /**
     * The total amount of VAT for a single unit
     * of the ordered product.
     *
     * @var float | null
     */
    protected $unitVat;
    /**
     * Total value of the order line including VAT.
     *
     * @var float | null
     */
    protected $lineTotalInclVat;
    /**
     * Total amount of VAT charged over
     * the total value of the order line.
     *
     * @var float | null
     */
    protected $lineVat;
    /**
     * The value of a single unit of
     * the ordered product including VAT.
     *
     * @var float | null
     */
    protected $originalUnitPriceInclVat;
    /**
     * The total amount of VAT charged over
     * the value of a single unit of the ordered product
     *
     * @var float | null
     */
    protected $originalUnitVat;
    /**
     * The total value of the order line including VAT.
     *
     * @var float | null
     */
    protected $originalLineTotalInclVat;
    /**
     * The total amount of VAT charged over
     * the total value of the order line.
     *
     * @var float | null
     */
    protected $originalLineVat;
    /**
     * A percentage fee that is charged
     * by the Channel for this order line.
     *
     * @var float
     */
    protected $originalFeeFixed;
    /**
     * If the product is ordered part of a bundle,
     * this field contains the MerchantProductNo of the product bundle
     *
     * @var string | null
     */
    protected $bundleProductMerchantProductNo;
    /**
     * @var ExtraData[] | null
     */
    protected $extraData;
    /**
     * The unique product reference used by the channel.
     *
     * @var string
     */
    protected $channelProductNo;
    /**
     * The unique product reference used by the merchant.
     *
     * @var string | null
     */
    protected $merchantProductNo;
    /**
     * The number of items of the product.
     *
     * @var int
     */
    protected $quantity;
    /**
     * The number of items for which cancellation was requested by the customer.
     *
     * @var int
     */
    protected $cancellationRequestedQuantity;
    /**
     * The value of a single unit of the ordered product including VAT.
     *
     * @var float
     */
    protected $unitPriceInclVat;
    /**
     * A fixed fee that is charged by the Channel for this order line.
     *
     * @var float
     */
    protected $feeFixed;
    /**
     * A percentage fee that is charged by the Channel for this order line.
     *
     * @var float
     */
    protected $feeRate;
    /**
     * Available conditions:
     * NEW
     * NEW_REFURBISHED
     * USED_AS_NEW
     * USED_GOOD
     * USED_REASONABLE
     * USED_MEDIOCRE
     * UNKNOWN
     * USED_VERY_GOOD
     *
     * @var string
     */
    protected $condition;
    /**
     * Expected delivery date from channels,
     * empty if channels not support this value.
     *
     * @var DateTime | null
     */
    protected $expectedDeliveryDate;
    /**
     * @var float
     */
    protected $vatRate;

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
     * @return array|null
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param array|null $extraData
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

    public static function fromArray(array $data)
    {
        $lineItem = new static();
        $lineItem->setStatus(static::getDataValue($data, 'Status', null));
        $lineItem->setIsFulfillmentByMarketplace(static::getDataValue($data, 'IsFulfillmentByMarketplace', null));
        $lineItem->setGtin(static::getDataValue($data, 'Gtin', null));
        $lineItem->setDescription(static::getDataValue($data, 'Description', null));
        $lineItem->setUnitVat(static::getDataValue($data, 'UnitVat', null));
        $lineItem->setLineTotalInclVat(static::getDataValue($data, 'LineTotalInclVat', null));
        $lineItem->setLineVat(static::getDataValue($data, 'LineVat', null));
        $lineItem->setOriginalUnitPriceInclVat(static::getDataValue($data, 'OriginalUnitPriceInclVat', null));
        $lineItem->setOriginalUnitVat(static::getDataValue($data, 'OriginalUnitVat', null));
        $lineItem->setOriginalLineTotalInclVat(static::getDataValue($data, 'OriginalLineTotalInclVat', null));
        $lineItem->setOriginalLineVat(static::getDataValue($data, 'OriginalLineVat', null));
        $lineItem->setOriginalFeeFixed(static::getDataValue($data, 'OriginalFeeFixed', null));
        $lineItem->setBundleProductMerchantProductNo(static::getDataValue($data, 'BundleProductMerchantProductNo', null));
        $lineItem->setChannelProductNo(static::getDataValue($data, 'ChannelProductNo', null));
        $lineItem->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo', null));
        $lineItem->setQuantity(static::getDataValue($data, 'Quantity', null));
        $lineItem->setCancellationRequestedQuantity(static::getDataValue($data, 'CancellationRequestedQuantity', null));
        $lineItem->setUnitPriceInclVat(static::getDataValue($data, 'UnitPriceInclVat', null));
        $lineItem->setFeeFixed(static::getDataValue($data, 'FeeFixed', null));
        $lineItem->setFeeRate(static::getDataValue($data, 'FeeRate', null));
        $lineItem->setCondition(static::getDataValue($data, 'Condition', null));
        $lineItem->setExpectedDeliveryDate(static::getDate(static::getDataValue($data, 'ExpectedDeliveryDate', null)));
        $lineItem->setExtraData(ExtraData::fromBatch(static::getDataValue($data, 'ExtraData', [])));
        $lineItem->setVatRate(static::getDataValue($data, 'VatRate', 0));

        return $lineItem;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $extraDataArray = [];
        $extraData = $this->getExtraData();

        foreach ($extraData as $item) {
            $extraDataArray[] = $item->toArray();
        }

        return [
            'Status' => $this->getStatus(),
            'IsFulfillmentByMarketplace' => $this->isFulfillmentByMarketplace(),
            'Gtin' => $this->getGtin(),
            'Description' => $this->getDescription(),
            'UnitVat' => $this->getUnitVat(),
            'LineTotalInclVat' => $this->getLineTotalInclVat(),
            'LineVat' => $this->getLineVat(),
            'OriginalUnitPriceInclVat' => $this->getOriginalUnitPriceInclVat(),
            'OriginalUnitVat' => $this->getOriginalUnitVat(),
            'OriginalLineTotalInclVat' => $this->getOriginalLineTotalInclVat(),
            'OriginalLineVat' => $this->getOriginalLineVat(),
            'OriginalFeeFixed' => $this->getOriginalFeeFixed(),
            'BundleProductMerchantProductNo' => $this->getBundleProductMerchantProductNo(),
            'VatRate' => $this->getVatRate(),
            'ExtraData' => $extraDataArray,
            'ChannelProductNo' => $this->getChannelProductNo(),
            'MerchantProductNo' => $this->getMerchantProductNo(),
            'Quantity' => $this->getQuantity(),
            'CancellationRequestedQuantity' => $this->getCancellationRequestedQuantity(),
            'UnitPriceInclVat' => $this->getUnitPriceInclVat(),
            'FeeFixed' => $this->getFeeFixed(),
            'FeeRate' => $this->getFeeRate(),
            'Condition' => $this->getCondition(),
            'ExpectedDeliveryDate' => substr(date_format($this->getExpectedDeliveryDate(), 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
        ];
    }
}