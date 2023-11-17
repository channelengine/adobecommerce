<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class Line
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class Line extends DataTransferObject
{
    /**
     * @var string
     */
    private $merchantProductNo;
    /**
     * @var string
     */
    private $channelProductNo;
    /**
     * @var MerchantShipmentLineResponse
     */
    private $orderLine;
    /**
     * @var string
     */
    private $shipmentStatus;
    /**
     * @var ExtraData[]
     */
    private $extraData;
    /**
     * @var int
     */
    private $quantity;

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
     * @return MerchantShipmentLineResponse
     */
    public function getOrderLine()
    {
        return $this->orderLine;
    }

    /**
     * @param MerchantShipmentLineResponse $orderLine
     */
    public function setOrderLine($orderLine)
    {
        $this->orderLine = $orderLine;
    }

    /**
     * @return string
     */
    public function getShipmentStatus()
    {
        return $this->shipmentStatus;
    }

    /**
     * @param string $shipmentStatus
     */
    public function setShipmentStatus($shipmentStatus)
    {
        $this->shipmentStatus = $shipmentStatus;
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
     * @inheritDoc
     */
    public function toArray()
    {
        $extraData = null;

        foreach ($this->extraData as $data) {
            $extraData[] = $data->toArray();
        }

        return [
            'MerchantProductNo' => $this->merchantProductNo,
            'ChannelProductNo' => $this->channelProductNo,
            'OrderLine' => $this->orderLine->toArray(),
            'ShipmentStatus' => $this->shipmentStatus,
            'ExtraData' => $extraData,
            'Quantity' => $this->quantity,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $line = new self();
        $line->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
        $line->setChannelProductNo(static::getDataValue($data, 'ChannelProductNo'));
        $line->setOrderLine(MerchantShipmentLineResponse::fromArray(static::getDataValue($data, 'OrderLine', [])));
        $line->setShipmentStatus(static::getDataValue($data, 'ShipmentStatus'));
        $line->setExtraData(ExtraData::fromBatch(static::getDataValue($data, 'ExtraData', [])));
        $line->setQuantity(static::getDataValue($data, 'Quantity', 0));

        return $line;
    }
}