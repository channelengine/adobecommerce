<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\CreateShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\OrderItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\RejectResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\UpdateShipmentRequest;
use Exception;

/**
 * Interface ShipmentsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts
 */
interface ShipmentsService
{
    /**
     * Retrieves all order items by order id.
     *
     * @param string $shopOrderId
     *
     * @return OrderItem[]
     */
    public function getAllItems($shopOrderId);

    /**
     * Rejects creation request.
     *
     * @param CreateShipmentRequest $request
     * @param Exception $reason
     *
     * @return RejectResponse
     */
    public function rejectCreate($request, Exception $reason);

    /**
     * Rejects update request.
     *
     * @param UpdateShipmentRequest $request
     * @param Exception $reason
     *
     * @return RejectResponse
     */
    public function rejectUpdate($request, Exception $reason);
}