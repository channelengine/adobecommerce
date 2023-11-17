<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\Transformer;

class Cancellation extends DataTransferObject
{
    /**
     * @var string
     */
    protected $merchantCancellationNo;
    /**
     * @var string
     */
    protected $merchantOrderNo;
    /**
     * @var string | null
     */
    protected $reason;
    /**
     * @var string One of [ NOT_IN_STOCK, DAMAGED, INCOMPLETE, CLIENT_CANCELLED, INVALID_ADDRESS, OTHER ].
     */
    protected $reasonCode;

    /**
     * @var Line[]
     */
    protected $lines;

    /**
     * @return string
     */
    public function getMerchantCancellationNo()
    {
        return $this->merchantCancellationNo;
    }

    /**
     * @param string $merchantCancellationNo
     */
    public function setMerchantCancellationNo($merchantCancellationNo)
    {
        $this->merchantCancellationNo = $merchantCancellationNo;
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
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param string $reasonCode
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;
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

    public function toArray()
    {
        return [
            'MerchantCancellationNo' => $this->getMerchantCancellationNo(),
            'MerchantOrderNo' => $this->getMerchantOrderNo(),
            'Reason' => $this->getReason(),
            'ReasonCode' => $this->getReasonCode(),
            'Lines' => Transformer::batchTransform($this->getLines()),
        ];
    }

    public static function fromArray(array $data)
    {
        $entity = new static();
        $entity->setMerchantCancellationNo(static::getDataValue($data, 'MerchantCancellationNo'));
        $entity->setMerchantOrderNo(static::getDataValue($data, 'MerchantOrderNo'));
        $entity->setReason(static::getDataValue($data, 'Reason', null));
        $entity->setReasonCode(static::getDataValue($data, 'ReasonCode'));
        $entity->setLines(Line::fromBatch(static::getDataValue($data, 'Lines', [])));

        return $entity;
    }
}