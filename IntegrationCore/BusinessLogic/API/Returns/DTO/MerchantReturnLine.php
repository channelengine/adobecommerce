<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantReturnLine
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class MerchantReturnLine extends DataTransferObject
{
    /**
     * @var string
     */
    private $merchantProductNo;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var array
     */
    private $extraData;

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
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param array $extraData
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'MerchantProductNo' => $this->merchantProductNo,
            'Quantity' => $this->quantity,
            'ExtraData' => $this->extraData,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
       $line = new self();

       $line->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
       $line->setQuantity(static::getDataValue($data, 'Quantity', 0));
       $line->setExtraData(static::getDataValue($data, 'ExtraData', []));

       return $line;
    }
}