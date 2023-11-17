<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\Enums;

/**
 * Interface EventTypes
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\Enums
 */
interface EventTypes
{
    const ORDERS_CREATE = "ORDERS_CREATE";
    const PRODUCTS_CHANGE = "PRODUCTS_CHANGE";
    const RETURNS_CHANGE = "RETURNS_CHANGE";
    const SHIPMENTS_CHANGE = "SHIPMENTS_CHANGE";
    const NOTIFICATIONS_CHANGE = "NOTIFICATIONS_CHANGE";
    const BUNDLE_PRODUCTS_CHANGE = "BUNDLE_PRODUCTS_CHANGE";
}