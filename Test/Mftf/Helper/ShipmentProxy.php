<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO\MerchantShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\Http\Proxy;

/**
 * Class ShipmentProxy
 *
 * @package  ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper
 */
class ShipmentProxy extends Proxy
{
    /**
     * @Override
     */
    public function createShipment(MerchantShipmentRequest $shipmentRequest)
    {
    }
}