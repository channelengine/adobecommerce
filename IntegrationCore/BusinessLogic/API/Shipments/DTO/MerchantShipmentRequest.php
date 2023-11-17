<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantShipmentRequest
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class MerchantShipmentRequest extends DataTransferObject
{
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
     * Merchant shipment line request.
     *
     * @var MerchantShipmentLineRequest[]
     */
    protected $lines;
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
     * @return MerchantShipmentLineRequest[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param MerchantShipmentLineRequest[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
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

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $lines = [];

        foreach ($this->getLines() as $line) {
            $lines[] = $line->toArray();
        }

        return [
            'MerchantShipmentNo' => $this->getMerchantShipmentNo(),
            'MerchantOrderNo' => $this->getMerchantOrderNo(),
            'Lines' => $lines,
            'TrackTraceNo' => $this->getTrackTraceNo(),
            'TrackTraceUrl' => $this->getTrackTraceUrl(),
            'ReturnTrackTraceNo' => $this->getReturnTrackTraceNo(),
            'Method' => $this->getMethod(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $request = new static();

        $request->setMerchantShipmentNo(static::getDataValue($data, 'MerchantShipmentNo', ''));
        $request->setMerchantOrderNo(static::getDataValue($data, 'MerchantOrderNo', ''));
        $request->setTrackTraceNo(static::getDataValue($data, 'TrackTraceNo', ''));
        $request->setTrackTraceUrl(static::getDataValue($data, 'TrackTraceUrl', ''));
        $request->setReturnTrackTraceNo(static::getDataValue($data, 'ReturnTrackTraceNo', ''));
        $request->setMethod(static::getDataValue($data, 'Method', ''));
        $request->setLines(MerchantShipmentLineRequest::fromBatch(static::getDataValue($data, 'Lines', [])));

        return $request;
    }
}
