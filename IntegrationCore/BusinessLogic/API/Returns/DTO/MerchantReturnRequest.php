<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantReturnRequest
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class MerchantReturnRequest extends DataTransferObject
{
    /**
     * @var string
     */
    private $merchantOrderNo;
    /**
     * @var string
     */
    private $merchantReturnNo;
    /**
     * @var MerchantReturnLine[]
     */
    private $lines;
    /**
     * @var int
     */
    private $id;
    /**
     * Available reasons:
     * PRODUCT_DEFECT
     * PRODUCT_UNSATISFACTORY
     * WRONG_PRODUCT
     * TOO_MANY_PRODUCTS
     * REFUSED
     * REFUSED_DAMAGED
     * WRONG_ADDRESS
     * NOT_COLLECTED
     * WRONG_SIZE
     * OTHER
     *
     * @var string
     */
    private $reason;
    /**
     * @var string
     */
    private $customerComment;
    /**
     * @var string
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
     * @var string
     */
    private $returnDate;
    /**
     * @var array
     */
    private $extraData;

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
    public function getMerchantReturnNo()
    {
        return $this->merchantReturnNo;
    }

    /**
     * @param string $merchantReturnNo
     */
    public function setMerchantReturnNo($merchantReturnNo)
    {
        $this->merchantReturnNo = $merchantReturnNo;
    }

    /**
     * @return MerchantReturnLine[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param MerchantReturnLine[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
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
     * @return string
     */
    public function getCustomerComment()
    {
        return $this->customerComment;
    }

    /**
     * @param string $customerComment
     */
    public function setCustomerComment($customerComment)
    {
        $this->customerComment = $customerComment;
    }

    /**
     * @return string
     */
    public function getMerchantComment()
    {
        return $this->merchantComment;
    }

    /**
     * @param string $merchantComment
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
     * @return string
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param string $returnDate
     */
    public function setReturnDate($returnDate)
    {
        $this->returnDate = $returnDate;
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
        $lines = [];

        foreach ($this->lines as $line) {
            $lines[] = $line->toArray();
        }

        return [
            'MerchantOrderNo' => $this->merchantOrderNo,
            'MerchantReturnNo' => $this->merchantReturnNo,
            'Lines' => $lines,
            'Id' => $this->id,
            'Reason' => $this->reason,
            'CustomerComment' => $this->customerComment,
            'MerchantComment' => $this->merchantComment,
            'RefundInclVat' => $this->refundInclVat,
            'RefundExclVat' => $this->refundExclVat,
            'ReturnDate' => $this->returnDate,
            'ExtraData' => $this->extraData,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $returnRequest = new self();

        $returnRequest->setMerchantOrderNo(static::getDataValue($data, 'MerchantOrderNo'));
        $returnRequest->setMerchantReturnNo(static::getDataValue($data, 'MerchantReturnNo'));
        $returnRequest->setLines(MerchantReturnLine::fromBatch(static::getDataValue($data, 'Lines', [])));
        $returnRequest->setId(static::getDataValue($data, 'Id', 0));
        $returnRequest->setReason(static::getDataValue($data, 'Reason'));
        $returnRequest->setCustomerComment(static::getDataValue($data, 'CustomerComment'));
        $returnRequest->setMerchantComment(static::getDataValue($data, 'MerchantComment'));
        $returnRequest->setRefundInclVat(static::getDataValue($data, 'RefundInclVat', 0));
        $returnRequest->setRefundExclVat(static::getDataValue($data, 'RefundExclVat', 0));
        $returnRequest->setReturnDate(static::getDataValue($data, 'ReturnDate'));
        $returnRequest->setExtraData(static::getDataValue($data, 'ExtraData', null));

        return $returnRequest;
    }
}