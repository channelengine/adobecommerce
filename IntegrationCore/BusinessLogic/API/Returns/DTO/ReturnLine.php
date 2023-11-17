<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ReturnLine
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class ReturnLine extends DataTransferObject
{
    /**
     * @var string | null
     */
    private $merchantProductNo;
    /**
     * @var OrderLine
     */
    private $orderLine;
    /**
     * @var string
     */
    private $shipmentStatus;
    /**
     * @var int
     */
    private $quantity;

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
     * @return OrderLine
     */
    public function getOrderLine()
    {
        return $this->orderLine;
    }

    /**
     * @param OrderLine $orderLine
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
        return [
            'MerchantProductNo' => $this->merchantProductNo,
            'OrderLine' => $this->orderLine->toArray(),
            'ShipmentStatus' => $this->shipmentStatus,
            'Quantity' => $this->quantity,
        ];
    }

    public static function fromArray(array $data)
    {
        $returnLine = new self();

        $returnLine->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
        $returnLine->setOrderLine(OrderLine::fromArray(static::getDataValue($data, 'OrderLine', [])));
        $returnLine->setShipmentStatus(static::getDataValue($data, 'ShipmentStatus'));
        $returnLine->setQuantity(static::getDataValue($data, 'Quantity', 0));

        return $returnLine;
    }
}