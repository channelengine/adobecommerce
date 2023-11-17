<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class Shipment
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class Shipment extends DataTransferObject
{
    /**
     * @var string
     */
    private $merchantShipmentNo;
    /**
     * @var string
     */
    private $merchantOrderNo;
    /**
     * @var string
     */
    private $channelShipmentNo;
    /**
     * @var string
     */
    private $channelOrderNo;
    /**
     * @var Line[]
     */
    private $lines;
    /**
     * @var string
     */
    private $createdAt;
    /**
     * @var string
     */
    private $updatedAt;
    /**
     * @var ExtraData[]
     */
    private $extraData;
    /**
     * @var string
     */
    private $trackTraceNo;
    /**
     * @var string
     */
    private $trackTraceUrl;
    /**
     * @var string
     */
    private $returnTrackTraceNo;
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $shippedFromCountryCode;
    /**
     * @var string
     */
    private $shipmentDate;

    /**
     * @return string
     */
    public function getMerchantShipmentNo()
    {
        return $this->merchantShipmentNo;
    }

    /**
     * @param string $merchantShipmentNo
     */
    public function setMerchantShipmentNo($merchantShipmentNo)
    {
        $this->merchantShipmentNo = $merchantShipmentNo;
    }

    /**
     * @return string
     */
    public function getMerchantOrderNo()
    {
        return $this->merchantOrderNo;
    }

    /**
     * @param string $merchantOrderNo
     */
    public function setMerchantOrderNo($merchantOrderNo)
    {
        $this->merchantOrderNo = $merchantOrderNo;
    }

    /**
     * @return string
     */
    public function getChannelShipmentNo()
    {
        return $this->channelShipmentNo;
    }

    /**
     * @param string $channelShipmentNo
     */
    public function setChannelShipmentNo($channelShipmentNo)
    {
        $this->channelShipmentNo = $channelShipmentNo;
    }

    /**
     * @return string
     */
    public function getChannelOrderNo()
    {
        return $this->channelOrderNo;
    }

    /**
     * @param string $channelOrderNo
     */
    public function setChannelOrderNo($channelOrderNo)
    {
        $this->channelOrderNo = $channelOrderNo;
    }

    /**
     * @return Line[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param Line[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
    public function getTrackTraceNo()
    {
        return $this->trackTraceNo;
    }

    /**
     * @param string $trackTraceNo
     */
    public function setTrackTraceNo($trackTraceNo)
    {
        $this->trackTraceNo = $trackTraceNo;
    }

    /**
     * @return string
     */
    public function getTrackTraceUrl()
    {
        return $this->trackTraceUrl;
    }

    /**
     * @param string $trackTraceUrl
     */
    public function setTrackTraceUrl($trackTraceUrl)
    {
        $this->trackTraceUrl = $trackTraceUrl;
    }

    /**
     * @return string
     */
    public function getReturnTrackTraceNo()
    {
        return $this->returnTrackTraceNo;
    }

    /**
     * @param string $returnTrackTraceNo
     */
    public function setReturnTrackTraceNo($returnTrackTraceNo)
    {
        $this->returnTrackTraceNo = $returnTrackTraceNo;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getShippedFromCountryCode()
    {
        return $this->shippedFromCountryCode;
    }

    /**
     * @param string $shippedFromCountryCode
     */
    public function setShippedFromCountryCode($shippedFromCountryCode)
    {
        $this->shippedFromCountryCode = $shippedFromCountryCode;
    }

    /**
     * @return string
     */
    public function getShipmentDate()
    {
        return $this->shipmentDate;
    }

    /**
     * @param string $shipmentDate
     */
    public function setShipmentDate($shipmentDate)
    {
        $this->shipmentDate = $shipmentDate;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $lines = [];

        foreach ($this->lines as $line) {
            $lines[] = $line->toArray();
        }

        $extraData = null;

        foreach ($this->extraData as $data) {
            $extraData[] = $data->toArray();
        }

        return [
            'MerchantShipmentNo' => $this->merchantShipmentNo,
            'MerchantOrderNo' => $this->merchantOrderNo,
            'ChannelShipmentNo' => $this->channelShipmentNo,
            'ChannelOrderNo' => $this->channelOrderNo,
            'Lines' => $lines,
            'CreatedAt' => $this->createdAt,
            'UpdatedAt' => $this->updatedAt,
            'ExtraData' => $extraData,
            'TrackTraceNo' => $this->trackTraceNo,
            'TrackTraceUrl' => $this->trackTraceUrl,
            'ReturnTrackTraceNo' => $this->returnTrackTraceNo,
            'Method' => $this->method,
            'ShippedFromCountryCode' => $this->shippedFromCountryCode,
            'ShipmentDate' => $this->shipmentDate,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $shipment = new self();
        $shipment->setMerchantShipmentNo(static::getDataValue($data, 'MerchantShipmentNo'));
        $shipment->setMerchantOrderNo(static::getDataValue($data, 'MerchantOrderNo'));
        $shipment->setChannelShipmentNo(static::getDataValue($data, 'ChannelShipmentNo', null));
        $shipment->setChannelOrderNo(static::getDataValue($data, 'ChannelOrderNo'));
        $shipment->setLines(Line::fromBatch(static::getDataValue($data, 'Lines', [])));
        $shipment->setCreatedAt(static::getDataValue($data, 'CreatedAt'));
        $shipment->setUpdatedAt(static::getDataValue($data, 'UpdatedAt'));
        $shipment->setExtraData(ExtraData::fromBatch(static::getDataValue($data, 'ExtraData', [])));
        $shipment->setTrackTraceNo(static::getDataValue($data, 'TrackTraceNo'));
        $shipment->setTrackTraceUrl(static::getDataValue($data, 'TrackTraceUrl', null));
        $shipment->setReturnTrackTraceNo(static::getDataValue($data, 'ReturnTrackTraceNo'));
        $shipment->setMethod(static::getDataValue($data, 'Method'));
        $shipment->setShippedFromCountryCode(static::getDataValue($data, 'ShippedFromCountryCode', null));
        $shipment->setShipmentDate(static::getDataValue($data, 'ShipmentDate', null));

        return $shipment;
    }
}