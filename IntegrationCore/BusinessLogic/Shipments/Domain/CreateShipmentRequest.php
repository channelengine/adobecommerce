<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain;

/**
 * Class CreateShipmentRequest
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain
 */
class CreateShipmentRequest
{
    /**
     * @var string
     */
    protected $shopOrderId;
    /**
     * @var OrderItem[]
     */
    protected $orderItems;
    /**
     * @var bool
     */
    protected $isPartial;
    /**
     * Unique shipment reference used by the merchant.
     *
     * @var string
     */
    protected $merchantShipmentNo;
    /**
     * Unique order reference used by the merchant.
     *
     * @var string
     */
    protected $merchantOrderNo;
    /**
     * Unique shipping reference used by the shipping carrier
     * (track and trace number).
     *
     * @var string | null
     */
    protected $trackTraceNo;
    /**
     * A link to a page of the carrier where the customer can
     * track the shipping of their package.
     *
     * @var string | null
     */
    protected $trackTraceUrl;
    /**
     * Unique return shipping reference that may be used by the
     * shipping carrier (track and trace number) if the shipment is returned.
     *
     * @var string | null
     */
    protected $returnTrackTraceNo;
    /**
     * Shipment method: the carrier used for shipping the package.
     *
     * @var string | null
     */
    protected $method;

    /**
     * CreateShipmentRequest constructor.
     *
     * @param string $shopOrderId
     * @param OrderItem[] $orderItems
     * @param bool $isPartial
     * @param string $merchantShipmentNo
     * @param string $merchantOrderNo
     * @param string|null $trackTraceNo
     * @param string|null $trackTraceUrl
     * @param string|null $returnTrackTraceNo
     * @param string|null $method
     */
    public function __construct(
        $shopOrderId,
        array $orderItems,
        $isPartial,
        $merchantShipmentNo,
        $merchantOrderNo,
        $trackTraceNo = '',
        $trackTraceUrl = '',
        $returnTrackTraceNo = '',
        $method = ''
    )
    {
        $this->shopOrderId = $shopOrderId;
        $this->orderItems = $orderItems;
        $this->isPartial = $isPartial;
        $this->merchantShipmentNo = $merchantShipmentNo;
        $this->merchantOrderNo = $merchantOrderNo;
        $this->trackTraceNo = $trackTraceNo;
        $this->trackTraceUrl = $trackTraceUrl;
        $this->returnTrackTraceNo = $returnTrackTraceNo;
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getShopOrderId()
    {
        return $this->shopOrderId;
    }

    /**
     * @param string $shopOrderId
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->shopOrderId = $shopOrderId;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem[] $orderItems
     */
    public function setOrderItems($orderItems)
    {
        $this->orderItems = $orderItems;
    }

    /**
     * @return bool
     */
    public function isPartial()
    {
        return $this->isPartial;
    }

    /**
     * @param bool $isPartial
     */
    public function setIsPartial($isPartial)
    {
        $this->isPartial = $isPartial;
    }

    /**
     * @return string
     */
    public function getMerchantShipmentNo()
    {
        return $this->merchantShipmentNo;
    }

    /**
     * @param string $merchantShipmentNo
     */
    public function setMerchantShipmentNo($merchantShipmentNo)
    {
        $this->merchantShipmentNo = $merchantShipmentNo;
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
    public function getTrackTraceNo()
    {
        return $this->trackTraceNo;
    }

    /**
     * @param string|null $trackTraceNo
     */
    public function setTrackTraceNo($trackTraceNo)
    {
        $this->trackTraceNo = $trackTraceNo;
    }

    /**
     * @return string|null
     */
    public function getTrackTraceUrl()
    {
        return $this->trackTraceUrl;
    }

    /**
     * @param string|null $trackTraceUrl
     */
    public function setTrackTraceUrl($trackTraceUrl)
    {
        $this->trackTraceUrl = $trackTraceUrl;
    }

    /**
     * @return string|null
     */
    public function getReturnTrackTraceNo()
    {
        return $this->returnTrackTraceNo;
    }

    /**
     * @param string|null $returnTrackTraceNo
     */
    public function setReturnTrackTraceNo($returnTrackTraceNo)
    {
        $this->returnTrackTraceNo = $returnTrackTraceNo;
    }

    /**
     * @return string|null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string|null $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
}