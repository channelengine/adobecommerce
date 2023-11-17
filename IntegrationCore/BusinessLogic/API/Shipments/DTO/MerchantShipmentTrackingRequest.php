<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantShipmentTrackingRequest
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class MerchantShipmentTrackingRequest extends DataTransferObject
{
    /**
     * Shipment method (carrier).
     *
     * @var string
     */
    protected $method;
    /**
     * Unique shipping reference used by the shipping carrier
     * (track and trace number).
     *
     * @var string
     */
    protected $trackTraceNo;
    /**
     * Unique return shipping reference that may be used by
     * the shipping carrier (track and trace number)
     * if the shipment is returned.
     *
     * @var string | null
     */
    protected $returnTrackTraceNo;
    /**
     * A link to a page of the carrier where the customer
     * can track the shipping of their package.
     *
     * @var string | null
     */
    protected $trackTraceUrl;

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

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'Method' => $this->getMethod(),
            'TrackTraceNo' => $this->getTrackTraceNo(),
            'ReturnTrackTraceNo' => $this->getReturnTrackTraceNo(),
            'TrackTraceUrl' => $this->getTrackTraceUrl(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $request = new static();

        $request->setMethod(static::getDataValue($data, 'Method', ''));
        $request->setReturnTrackTraceNo(static::getDataValue($data, 'ReturnTrackTraceNo', ''));
        $request->setTrackTraceNo(static::getDataValue($data, 'TrackTraceNo', ''));
        $request->setTrackTraceUrl(static::getDataValue($data, 'TrackTraceUrl', ''));

        return $request;
    }
}
