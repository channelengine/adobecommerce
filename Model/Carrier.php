<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

/**
 * Class Carrier
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'channelengine_carrier';
    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @param RateRequest $request
     *
     * @return bool|null
     */
    public function collectRates(RateRequest $request): ?bool
    {
        return false;
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable(): bool
    {
        return true;
    }

    /**
     * Get allowed shipping methods.
     *
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
