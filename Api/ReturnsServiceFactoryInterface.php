<?php

namespace ChannelEngine\ChannelEngineIntegration\Api;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\ReturnsService;

/**
 * Interface ReturnsServiceFactoryInterface
 *
 * @package ChannelEngine\ChannelEngineIntegration\Api
 */
interface ReturnsServiceFactoryInterface
{
    /**
     * Creates an instance of ReturnsService based on shop edition.
     *
     * @return ReturnsService
     */
    public function create(): ReturnsService;
}
