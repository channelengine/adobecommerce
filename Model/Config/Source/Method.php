<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\Config\Source;

use ChannelEngine\ChannelEngineIntegration\Model\Carrier;
use Magento\Shipping\Model\Carrier\Source\GenericInterface;

/**
 * Class Method
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\Config\Source
 */
class Method implements GenericInterface
{
    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * Method constructor.
     *
     * @param Carrier $carrier
     */
    public function __construct(Carrier $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $shippingMethods = $this->getShippingMethods();

        $arr = [];
        foreach ($shippingMethods as $code => $title) {
            $arr[] = ['value' => $code, 'label' => __($title)];
        }

        return $arr;
    }

    /**
     * Returns available shipping methods.
     *
     * @return array
     */
    protected function getShippingMethods(): array
    {
        return $this->carrier->getAllowedMethods();
    }
}
