<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Order;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Domain\CreateResponse;

/**
 * Interface OrdersService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Contracts
 */
interface OrdersService
{
    const CLASS_NAME = __CLASS__;

    /**
     * Creates new orders in the shop system and
     * returns CreateResponse.
     *
     * @param Order $order
     *
     * @return CreateResponse
     */
    public function create(Order $order);

	/**
	 * Retrieves total number of orders for synchronization.
	 *
	 * @return int
	 */
    public function getOrdersCount();
}
