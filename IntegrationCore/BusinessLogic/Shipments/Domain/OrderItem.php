<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class OrderItem
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain
 */
class OrderItem extends DataTransferObject
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
	 * @var bool
	 */
    protected $shipped;

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
     * @return bool
     */
    public function isShipped()
    {
        return $this->shipped;
    }

    /**
     * @param bool $shipped
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'MerchantProductNo' => $this->getMerchantProductNo(),
            'Quantity' => $this->getQuantity(),
            'Shipped' => $this->isShipped(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $orderItem = new static();

        $orderItem->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo', ''));
        $orderItem->setQuantity(static::getDataValue($data, 'Quantity', ''));
        $orderItem->setShipped(static::getDataValue($data, 'Shipped', false));

        return $orderItem;
    }
}
