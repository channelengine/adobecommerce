<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\RejectResponse;
use Exception;

/**
 * Interface CancellationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Contracts
 */
interface CancellationService
{
    /**
     * Provides list of cancelled items for order.
     *
     * @param $orderId
     *
     * @return CancellationItem[]
     */
    public function getAllItems($orderId);

    /**
     * Rejects cancellation request.
     *
     * @param CancellationRequest $request
     * @param Exception $reason
     *
     * @return RejectResponse
     */
    public function reject(CancellationRequest $request, Exception $reason);
}