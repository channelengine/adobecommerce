<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain;

/**
 * Class UpdateShipmentRequest
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain
 */
class UpdateShipmentRequest
{
    /**
     * @var string
     */
    protected $shopOrderId;
    /**
     * @var bool
     */
    protected $isPartial;
    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $trackTraceNo;
    /**
     * @var string | null
     */
    protected $returnTrackTraceNo;
    /**
     * @var string | null
     */
    protected $trackTraceUrl;

    /**
     * UpdateShipmentRequest constructor.
     *
     * @param string $shopOrderId
     * @param bool $isPartial
     * @param string $method
     * @param string $trackTraceNo
     * @param string|null $returnTrackTraceNo
     * @param string|null $trackTraceUrl
     */
    public function __construct(
        $shopOrderId,
        $isPartial,
        $method,
        $trackTraceNo,
        $returnTrackTraceNo,
        $trackTraceUrl
    )
    {
        $this->shopOrderId = $shopOrderId;
        $this->isPartial = $isPartial;
        $this->method = $method;
        $this->trackTraceNo = $trackTraceNo;
        $this->returnTrackTraceNo = $returnTrackTraceNo;
        $this->trackTraceUrl = $trackTraceUrl;
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
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getTrackTraceNo()
    {
        return $this->trackTraceNo;
    }

    /**
     * @param string $trackTraceNo
     */
    public function setTrackTraceNo($trackTraceNo)
    {
        $this->trackTraceNo = $trackTraceNo;
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
}