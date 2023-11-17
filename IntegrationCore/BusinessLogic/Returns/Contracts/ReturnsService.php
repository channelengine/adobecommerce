<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnUpdate;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\ReturnResponse;

/**
 * Class ReturnsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts
 */
interface ReturnsService
{
    /**
     * Creates return on ChannelEngine.
     *
     * @param MerchantReturnRequest $request
     *
     * @return void
     */
    public function createOnChannelEngine(MerchantReturnRequest $request);

    /**
     * Updates return on ChannelEngine.
     *
     * @param MerchantReturnUpdate $update
     *
     * @return void
     */
    public function update(MerchantReturnUpdate $update);

    /**
     * Creates return in shop.
     *
     * @param ReturnResponse $returnResponse
     *
     * @return void
     */
    public function createInShop(ReturnResponse $returnResponse);
}
