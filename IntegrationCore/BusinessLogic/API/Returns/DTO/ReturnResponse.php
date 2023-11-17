<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Data\TimestampAware;
use DateTime;

/**
 * Class ReturnResponse
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class ReturnResponse extends TimestampAware
{
    /**
     * @var string | null
     */
    private $merchantOrderNo;
    /**
     * @var string | null
     */
    private $channelOrderNo;
    /**
     * @var int | null
     */
    private $channelId;
    /**
     * @var string | null
     */
    private $globalChannelId;
    /**
     * @var string | null
     */
    private $globalChannelName;
    /**
     * @var ReturnLine[] | null
     */
    private $lines;
    /**
     * @var DateTime
     */
    private $createdAt;
    /**
     * @var DateTime
     */
    private $updatedAt;
    /**
     * @var string | null
     */
    private $merchantReturnNo;
    /**
     * @var string | null
     */
    private $channelReturnNo;
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $reason;
    /**
     * @var string | null
     */
    private $customerComment;
    /**
     * @var string | null
     */
    private $merchantComment;
    /**
     * @var float
     */
    private $refundInclVat;
    /**
     * @var float
     */
    private $refundExclVat;
    /**
     * @var DateTime | null
     */
    private $returnDate;

    /**
     * @return string|null
     */
    public function getMerchantOrderNo()
    {
        return $this->merchantOrderNo;
    }

    /**
     * @param string|null $merchantOrderNo
     */
    public function setMerchantOrderNo($merchantOrderNo)
    {
        $this->merchantOrderNo = $merchantOrderNo;
    }

    /**
     * @return string|null
     */
    public function getChannelOrderNo()
    {
        return $this->channelOrderNo;
    }

    /**
     * @param string|null $channelOrderNo
     */
    public function setChannelOrderNo($channelOrderNo)
    {
        $this->channelOrderNo = $channelOrderNo;
    }

    /**
     * @return int|null
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * @param int|null $channelId
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    }

    /**
     * @return string|null
     */
    public function getGlobalChannelId()
    {
        return $this->globalChannelId;
    }

    /**
     * @param string|null $globalChannelId
     */
    public function setGlobalChannelId($globalChannelId)
    {
        $this->globalChannelId = $globalChannelId;
    }

    /**
     * @return string|null
     */
    public function getGlobalChannelName()
    {
        return $this->globalChannelName;
    }

    /**
     * @param string|null $globalChannelName
     */
    public function setGlobalChannelName($globalChannelName)
    {
        $this->globalChannelName = $globalChannelName;
    }

    /**
     * @return ReturnLine[]|null
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param ReturnLine[]|null $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string|null
     */
    public function getMerchantReturnNo()
    {
        return $this->merchantReturnNo;
    }

    /**
     * @param string|null $merchantReturnNo
     */
    public function setMerchantReturnNo($merchantReturnNo)
    {
        $this->merchantReturnNo = $merchantReturnNo;
    }

    /**
     * @return string|null
     */
    public function getChannelReturnNo()
    {
        return $this->channelReturnNo;
    }

    /**
     * @param string|null $channelReturnNo
     */
    public function setChannelReturnNo($channelReturnNo)
    {
        $this->channelReturnNo = $channelReturnNo;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return string|null
     */
    public function getCustomerComment()
    {
        return $this->customerComment;
    }

    /**
     * @param string|null $customerComment
     */
    public function setCustomerComment($customerComment)
    {
        $this->customerComment = $customerComment;
    }

    /**
     * @return string|null
     */
    public function getMerchantComment()
    {
        return $this->merchantComment;
    }

    /**
     * @param string|null $merchantComment
     */
    public function setMerchantComment($merchantComment)
    {
        $this->merchantComment = $merchantComment;
    }

    /**
     * @return float
     */
    public function getRefundInclVat()
    {
        return $this->refundInclVat;
    }

    /**
     * @param float $refundInclVat
     */
    public function setRefundInclVat($refundInclVat)
    {
        $this->refundInclVat = $refundInclVat;
    }

    /**
     * @return float
     */
    public function getRefundExclVat()
    {
        return $this->refundExclVat;
    }

    /**
     * @param float $refundExclVat
     */
    public function setRefundExclVat($refundExclVat)
    {
        $this->refundExclVat = $refundExclVat;
    }

    /**
     * @return DateTime|null
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param DateTime|null $returnDate
     */
    public function setReturnDate($returnDate)
    {
        $this->returnDate = $returnDate;
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

        return [
            'MerchantOrderNo' => $this->merchantOrderNo,
            'ChannelOrderNo' => $this->channelOrderNo,
            'ChannelId' => $this->channelId,
            'GlobalChannelId' => $this->globalChannelId,
            'GlobalChannelName' => $this->globalChannelName,
            'Lines' => $lines,
            'CreatedAt' => substr(date_format($this->createdAt, 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
            'UpdatedAt' => substr(date_format($this->updatedAt, 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
            'MerchantReturnNo' => $this->merchantReturnNo,
            'ChannelReturnNo' => $this->channelReturnNo,
            'Id' => $this->id,
            'Reason' => $this->reason,
            'CustomerComment' => $this->customerComment,
            'MerchantComment' => $this->merchantComment,
            'RefundInclVat' => $this->refundInclVat,
            'RefundExclVat' => $this->refundExclVat,
            'ReturnDate' => substr(date_format($this->returnDate, 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $returnResponse = new self();

        $returnResponse->setMerchantOrderNo(static::getDataValue($data, 'MerchantOrderNo'));
        $returnResponse->setChannelOrderNo(static::getDataValue($data, 'ChannelOrderNo'));
        $returnResponse->setChannelId(static::getDataValue($data, 'ChannelId'));
        $returnResponse->setGlobalChannelId(static::getDataValue($data, 'GlobalChannelId'));
        $returnResponse->setGlobalChannelName(static::getDataValue($data, 'GlobalChannelName'));
        $returnResponse->setLines(ReturnLine::fromBatch(static::getDataValue($data, 'Lines', [])));
        $returnResponse->setCreatedAt(static::getDate(static::getDataValue($data, 'CreatedAt', null)));
        $returnResponse->setUpdatedAt(static::getDate(static::getDataValue($data, 'UpdatedAt', null)));
        $returnResponse->setMerchantReturnNo(static::getDataValue($data, 'MerchantReturnNo'));
        $returnResponse->setChannelReturnNo(static::getDataValue($data, 'ChannelReturnNo'));
        $returnResponse->setId(static::getDataValue($data, 'Id'));
        $returnResponse->setReason(static::getDataValue($data, 'Reason'));
        $returnResponse->setCustomerComment(static::getDataValue($data, 'CustomerComment'));
        $returnResponse->setMerchantComment(static::getDataValue($data, 'MerchantComment'));
        $returnResponse->setRefundInclVat(static::getDataValue($data, 'RefundInclVat', 0));
        $returnResponse->setRefundExclVat(static::getDataValue($data, 'RefundExclVat', 0));
        $returnResponse->setReturnDate(static::getDate(static::getDataValue($data, 'ReturnDate', null)));

        return $returnResponse;
    }
}