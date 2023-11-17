<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

class Line extends DataTransferObject
{
    /**
     * @var string
     */
    protected $merchantProductNo;
    /**
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

    public static function fromArray(array $data)
    {
        $entity = new static();
        $entity->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
        $entity->setQuantity(static::getDataValue($data, 'Quantity', 0));

        return $entity;
    }

    public function toArray()
    {
        return [
            'MerchantProductNo' => $this->getMerchantProductNo(),
            'Quantity' => $this->getQuantity(),
        ];
    }
}