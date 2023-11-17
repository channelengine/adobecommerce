<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Data\TimestampAware;
use DateTime;

/**
 * Class Order
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO
 */
class Order extends TimestampAware
{
    /**
     * The unique identifier used by ChannelEngine.
     *
     * @var int
     */
    protected $id;
    /**
     * The name of the channel for this specific environment/account.
     *
     * @var string | null
     */
    protected $channelName;

    /**
     * The unique ID of the channel for this specific environment/account.
     *
     * @var int | null
     */
    protected $channelId;
    /**
     * The name of the channel across all of ChannelEngine.
     *
     * @var string | null
     */
    protected $globalChannelName;
    /**
     * The unique ID of the channel across all of ChannelEngine.
     *
     * @var int | null
     */
    protected $globalChannelId;
    /**
     * Available order supports:
     * NONE
     * ORDERS
     * SPLIT_ORDERS
     * SPLIT_ORDER_LINES
     *
     * @var string
     */
    protected $channelOrderSupport;
    /**
     * The order reference used by the channel.
     *
     * @var string | null
     */
    protected $channelOrderNo;
    /**
     *
     * Type of fulfillment. Possible types:
     * Merchant
     * Marketplace
     *
     * @var string | null
     */
    protected $typeOfFulfillment;
    /**
     * Available statuses:
     * IN_PROGRESS
     * SHIPPED
     * IN_BACKORDER
     * MANCO
     * CANCELED
     * IN_COMBI
     * CLOSED
     * NEW
     * RETURNED
     * REQUIRES_CORRECTION
     *
     * @var string
     */
    protected $status;
    /**
     * Indicating whether the order is a business order.
     *
     * @var bool
     */
    protected $isBusinessOrder;
    /**
     * The date the order was created in ChannelEngine.
     *
     * @var DateTime | null
     */
    protected $createdAt;
    /**
     * The date the order was last updated in ChannelEngine.
     *
     * @var DateTime | null
     */
    protected $updatedAt;
    /**
     * The optional comment a merchant can add to an order.
     *
     * @var string | null
     */
    protected $merchantComment;
    /**
     * Billing address.
     *
     * @var Address
     */
    protected $billingAddress;
    /**
     * Shipping address.
     *
     * @var Address
     */
    protected $shippingAddress;
    /**
     * The total value of the order lines including VAT.
     *
     * @var float | null
     */
    protected $subTotalInclVat;
    /**
     * The total amount of VAT charged over the order lines.
     *
     * @var float | null
     */
    protected $subTotalVat;
    /**
     * The total amount of VAT charged over the shipping fee.
     *
     * @var float | null
     */
    protected $shippingCostsVat;
    /**
     * The total value of the order including VAT.
     *
     * @var float
     */
    protected $totalInclVat;
    /**
     * The total amount of VAT charged over the total value of te order.
     *
     * @var float | null
     */
    protected $totalVat;
    /**
     * The total value of the order lines including VAT.
     *
     * @var float | null
     */
    protected $originalSubTotalInclVat;
    /**
     * The total amount of VAT charged over the order lines.
     *
     * @var float | null
     */
    protected $originalSubTotalVat;
    /**
     * The shipping fee including VAT.
     *
     * @var float | null
     */
    protected $originalShippingCostsInclVat;
    /**
     * The total amount of VAT charged over the shipping fee.
     *
     * @var float | null
     */
    protected $originalShippingCostsVat;
    /**
     * The total value of the order including VAT.
     *
     * @var float | null
     */
    protected $originalTotalInclVat;
    /**
     * The total amount of VAT charged over the total value of te order.
     *
     * @var float | null
     */
    protected $originalTotalVat;
    /**
     * @var LineItem[]
     */
    protected $lines;
    /**
     * The customer's telephone number.
     *
     * @var string | null
     */
    protected $phone;
    /**
     * The customer's email.
     *
     * @var string
     */
    protected $email;
    /**
     * A company's chamber of commerce number.
     *
     * @var string | null
     */
    protected $companyRegistrationNo;
    /**
     * A company's VAT number.
     *
     * @var string | null
     */
    protected $vatNo;
    /**
     * The payment method used on the order.
     *
     * @var string | null
     */
    protected $paymentMethod;
    /**
     * The shipping fee including VAT.
     *
     * @var float
     */
    protected $shippingCostsInclVat;
    /**
     * The currency code for the amounts of the order.
     *
     * @var string
     */
    protected $currencyCode;
    /**
     * The date the order was created at the channel.
     *
     * @var DateTime
     */
    protected $orderDate;
    /**
     * The unique customer reference used by the channel.
     *
     * @var string | null
     */
    protected $channelCustomerNo;
    /**
     * Extra data on the order.
     *
     * @var array
     */
    protected $extraData;

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
     * @return string|null
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @param string|null $channelName
     */
    public function setChannelName($channelName)
    {
        $this->channelName = $channelName;
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
     * @return int|null
     */
    public function getGlobalChannelId()
    {
        return $this->globalChannelId;
    }

    /**
     * @param int|null $globalChannelId
     */
    public function setGlobalChannelId($globalChannelId)
    {
        $this->globalChannelId = $globalChannelId;
    }

    /**
     * @return string
     */
    public function getChannelOrderSupport()
    {
        return $this->channelOrderSupport;
    }

    /**
     * @param string $channelOrderSupport
     */
    public function setChannelOrderSupport($channelOrderSupport)
    {
        $this->channelOrderSupport = $channelOrderSupport;
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
     * @return string|null
     */
    public function getTypeOfFulfillment()
    {
        return $this->typeOfFulfillment;
    }

    /**
     * @param string|null $typeOfFulfillment
     */
    public function setTypeOfFulfillment($typeOfFulfillment)
    {
        $this->typeOfFulfillment = $typeOfFulfillment;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isBusinessOrder()
    {
        return $this->isBusinessOrder;
    }

    /**
     * @param bool $isBusinessOrder
     */
    public function setIsBusinessOrder($isBusinessOrder)
    {
        $this->isBusinessOrder = $isBusinessOrder;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param Address $shippingAddress
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return float|null
     */
    public function getSubTotalInclVat()
    {
        return $this->subTotalInclVat;
    }

    /**
     * @param float|null $subTotalInclVat
     */
    public function setSubTotalInclVat($subTotalInclVat)
    {
        $this->subTotalInclVat = $subTotalInclVat;
    }

    /**
     * @return float|null
     */
    public function getSubTotalVat()
    {
        return $this->subTotalVat;
    }

    /**
     * @param float|null $subTotalVat
     */
    public function setSubTotalVat($subTotalVat)
    {
        $this->subTotalVat = $subTotalVat;
    }

    /**
     * @return float|null
     */
    public function getShippingCostsVat()
    {
        return $this->shippingCostsVat;
    }

    /**
     * @param float|null $shippingCostsVat
     */
    public function setShippingCostsVat($shippingCostsVat)
    {
        $this->shippingCostsVat = $shippingCostsVat;
    }

    /**
     * @return float
     */
    public function getTotalInclVat()
    {
        return $this->totalInclVat;
    }

    /**
     * @param float $totalInclVat
     */
    public function setTotalInclVat($totalInclVat)
    {
        $this->totalInclVat = $totalInclVat;
    }

    /**
     * @return float|null
     */
    public function getTotalVat()
    {
        return $this->totalVat;
    }

    /**
     * @param float|null $totalVat
     */
    public function setTotalVat($totalVat)
    {
        $this->totalVat = $totalVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalSubTotalInclVat()
    {
        return $this->originalSubTotalInclVat;
    }

    /**
     * @param float|null $originalSubTotalInclVat
     */
    public function setOriginalSubTotalInclVat($originalSubTotalInclVat)
    {
        $this->originalSubTotalInclVat = $originalSubTotalInclVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalSubTotalVat()
    {
        return $this->originalSubTotalVat;
    }

    /**
     * @param float|null $originalSubTotalVat
     */
    public function setOriginalSubTotalVat($originalSubTotalVat)
    {
        $this->originalSubTotalVat = $originalSubTotalVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalShippingCostsInclVat()
    {
        return $this->originalShippingCostsInclVat;
    }

    /**
     * @param float|null $originalShippingCostsInclVat
     */
    public function setOriginalShippingCostsInclVat($originalShippingCostsInclVat)
    {
        $this->originalShippingCostsInclVat = $originalShippingCostsInclVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalShippingCostsVat()
    {
        return $this->originalShippingCostsVat;
    }

    /**
     * @param float|null $originalShippingCostsVat
     */
    public function setOriginalShippingCostsVat($originalShippingCostsVat)
    {
        $this->originalShippingCostsVat = $originalShippingCostsVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalTotalInclVat()
    {
        return $this->originalTotalInclVat;
    }

    /**
     * @param float|null $originalTotalInclVat
     */
    public function setOriginalTotalInclVat($originalTotalInclVat)
    {
        $this->originalTotalInclVat = $originalTotalInclVat;
    }

    /**
     * @return float|null
     */
    public function getOriginalTotalVat()
    {
        return $this->originalTotalVat;
    }

    /**
     * @param float|null $originalTotalVat
     */
    public function setOriginalTotalVat($originalTotalVat)
    {
        $this->originalTotalVat = $originalTotalVat;
    }

    /**
     * @return LineItem[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param LineItem[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getCompanyRegistrationNo()
    {
        return $this->companyRegistrationNo;
    }

    /**
     * @param string|null $companyRegistrationNo
     */
    public function setCompanyRegistrationNo($companyRegistrationNo)
    {
        $this->companyRegistrationNo = $companyRegistrationNo;
    }

    /**
     * @return string|null
     */
    public function getVatNo()
    {
        return $this->vatNo;
    }

    /**
     * @param string|null $vatNo
     */
    public function setVatNo($vatNo)
    {
        $this->vatNo = $vatNo;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string|null $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return float
     */
    public function getShippingCostsInclVat()
    {
        return $this->shippingCostsInclVat;
    }

    /**
     * @param float $shippingCostsInclVat
     */
    public function setShippingCostsInclVat($shippingCostsInclVat)
    {
        $this->shippingCostsInclVat = $shippingCostsInclVat;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * @param DateTime $orderDate
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
    }

    /**
     * @return string|null
     */
    public function getChannelCustomerNo()
    {
        return $this->channelCustomerNo;
    }

    /**
     * @param string|null $channelCustomerNo
     */
    public function setChannelCustomerNo($channelCustomerNo)
    {
        $this->channelCustomerNo = $channelCustomerNo;
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

    public static function fromArray(array $data)
    {
        $order = new self();

        $order->setId(static::getDataValue($data, 'Id', null));
        $order->setChannelName(static::getDataValue($data, 'ChannelName', null));
        $order->setChannelId(static::getDataValue($data, 'ChannelId', null));
        $order->setGlobalChannelName(static::getDataValue($data, 'GlobalChannelName', null));
        $order->setGlobalChannelId(static::getDataValue($data, 'GlobalChannelId', null));
        $order->setChannelOrderSupport(static::getDataValue($data, 'ChannelOrderSupport', null));
        $order->setChannelOrderNo(static::getDataValue($data, 'ChannelOrderNo', null));
        $order->setStatus(static::getDataValue($data, 'Status', null));
        $order->setIsBusinessOrder(static::getDataValue($data, 'IsBusinessOrder', null));
        $order->setCreatedAt(static::getDate(static::getDataValue($data, 'CreatedAt', null)));
        $order->setUpdatedAt(static::getDate(static::getDataValue($data, 'UpdatedAt', null)));
        $order->setMerchantComment(static::getDataValue($data, 'MerchantComment', null));
        $order->setBillingAddress(Address::fromArray(static::getDataValue($data, 'BillingAddress', [])));
        $order->setShippingAddress(Address::fromArray(static::getDataValue($data, 'ShippingAddress', [])));
        $order->setSubTotalInclVat(static::getDataValue($data, 'SubTotalInclVat', null));
        $order->setSubTotalVat(static::getDataValue($data, 'SubTotalVat', null));
        $order->setShippingCostsVat(static::getDataValue($data, 'ShippingCostsVat', null));
        $order->setTotalInclVat(static::getDataValue($data, 'TotalInclVat', null));
        $order->setTotalVat(static::getDataValue($data, 'TotalVat', null));
        $order->setOriginalSubTotalInclVat(static::getDataValue($data, 'OriginalSubTotalInclVat', null));
        $order->setOriginalSubTotalVat(static::getDataValue($data, 'OriginalSubTotalVat', null));
        $order->setOriginalShippingCostsInclVat(static::getDataValue($data, 'OriginalShippingCostsInclVat', null));
        $order->setOriginalShippingCostsVat(static::getDataValue($data, 'OriginalShippingCostsVat', null));
        $order->setOriginalTotalInclVat(static::getDataValue($data, 'OriginalTotalInclVat', null));
        $order->setOriginalTotalVat(static::getDataValue($data, 'OriginalTotalVat', null));
        $order->setLines(LineItem::fromBatch(static::getDataValue($data, 'Lines', [])));
        $order->setPhone(static::getDataValue($data, 'Phone', null));
        $order->setEmail(static::getDataValue($data, 'Email', null));
        $order->setCompanyRegistrationNo(static::getDataValue($data, 'CompanyRegistrationNo', null));
        $order->setVatNo(static::getDataValue($data, 'VatNo', null));
        $order->setPaymentMethod(static::getDataValue($data, 'PaymentMethod', null));
        $order->setShippingCostsInclVat(static::getDataValue($data, 'ShippingCostsInclVat', null));
        $order->setCurrencyCode(static::getDataValue($data, 'CurrencyCode', null));
        $order->setOrderDate(static::getDate(static::getDataValue($data, 'OrderDate', null)));
        $order->setChannelCustomerNo(static::getDataValue($data, 'ChannelCustomerNo', null));
        $order->setExtraData(static::getDataValue($data, 'ExtraData', []));

        return $order;
    }

    public function toArray()
    {
        $linesArray = [];
        $lines = $this->getLines();

        foreach ($lines as $line) {
            $linesArray[] = $line->toArray();
        }


        return [
            'Id' => $this->getId(),
            'ChannelName' => $this->getChannelName(),
            'ChannelId' => $this->getChannelId(),
            'GlobalChannelName' => $this->getGlobalChannelName(),
            'GlobalChannelId' => $this->getGlobalChannelId(),
            'ChannelOrderSupport' => $this->getChannelOrderSupport(),
            'ChannelOrderNo' => $this->getChannelOrderNo(),
            'Status' => $this->getStatus(),
            'IsBusinessOrder' => $this->isBusinessOrder(),
            'CreatedAt' => substr(date_format($this->getCreatedAt(), 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
            'UpdatedAt' => substr(date_format($this->getUpdatedAt(), 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
            'MerchantComment' => $this->getMerchantComment(),
            'BillingAddress' => $this->getBillingAddress()->toArray(),
            'ShippingAddress' => $this->getShippingAddress()->toArray(),
            'SubTotalInclVat' => $this->getSubTotalInclVat(),
            'SubTotalVat' => $this->getSubTotalVat(),
            'ShippingCostsVat' => $this->getShippingCostsVat(),
            'TotalInclVat' => $this->getTotalInclVat(),
            'TotalVat' => $this->getTotalVat(),
            'OriginalSubTotalInclVat' => $this->getOriginalSubTotalInclVat(),
            'OriginalSubTotalVat' => $this->getOriginalSubTotalVat(),
            'OriginalShippingCostsInclVat' => $this->getOriginalShippingCostsInclVat(),
            'OriginalShippingCostsVat' => $this->getOriginalShippingCostsVat(),
            'OriginalTotalInclVat' => $this->getOriginalTotalInclVat(),
            'OriginalTotalVat' => $this->getOriginalTotalVat(),
            'Lines' => $linesArray,
            'Phone' => $this->getPhone(),
            'Email' => $this->getEmail(),
            'CompanyRegistrationNo' => $this->getCompanyRegistrationNo(),
            'VatNo' => $this->getVatNo(),
            'PaymentMethod' => $this->getPaymentMethod(),
            'ShippingCostsInclVat' => $this->getShippingCostsInclVat(),
            'CurrencyCode' => $this->getCurrencyCode(),
            'OrderDate' => substr(date_format($this->getOrderDate(), 'Y-m-d\TH:i:s.u'), 0, -3) . 'Z',
            'ChannelCustomerNo' => $this->getChannelCustomerNo(),
            'ExtraData' => $this->getExtraData(),
        ];
    }
}