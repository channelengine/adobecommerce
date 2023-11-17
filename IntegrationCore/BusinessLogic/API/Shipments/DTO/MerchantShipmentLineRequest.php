<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantShipmentLineRequest
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class MerchantShipmentLineRequest extends DataTransferObject
{
    /**
     * Unique product reference used by the merchant (sku).
     *
     * @var string
     */
    protected $merchantProductNo;
    /**
     * Number of items of the product in the shipment.
     *
     * @var int
     */
    protected $quantity;

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
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'MerchantProductNo' => $this->getMerchantProductNo(),
            'Quantity' => $this->getQuantity(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $line = new static();

        $line->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo', ''));
        $line->setQuantity(static::getDataValue($data, 'Quantity', ''));

        return $line;
    }
}
