<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class Line
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class Line extends DataTransferObject
{
    /**
     * @var string
     */
    private $merchantProductNo;
    /**
     * @var int
     */
    private $acceptedQuantity;
    /**
     * @var int
     */
    private $rejectedQuantity;

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
    public function getAcceptedQuantity()
    {
        return $this->acceptedQuantity;
    }

    /**
     * @param int $acceptedQuantity
     */
    public function setAcceptedQuantity($acceptedQuantity)
    {
        $this->acceptedQuantity = $acceptedQuantity;
    }

    /**
     * @return int
     */
    public function getRejectedQuantity()
    {
        return $this->rejectedQuantity;
    }

    /**
     * @param int $rejectedQuantity
     */
    public function setRejectedQuantity($rejectedQuantity)
    {
        $this->rejectedQuantity = $rejectedQuantity;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'MerchantProductNo' => $this->merchantProductNo,
            'AcceptedQuantity' => $this->acceptedQuantity,
            'RejectedQuantity' => $this->rejectedQuantity,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $line = new self();

        $line->setMerchantProductNo(static::getDataValue($data, 'MerchantProductNo'));
        $line->setAcceptedQuantity(static::getDataValue($data, 'acceptedQuantity', 0));
        $line->setRejectedQuantity(static::getDataValue($data, 'rejectedQuantity', 0));

        return $line;
    }
}