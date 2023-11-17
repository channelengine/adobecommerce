<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnUpdate;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\ReturnResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\ReturnsService;

/**
 * Class NullReturnsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class NullReturnsService extends ReturnsService
{
    /**
     * @inheritDoc
     */
    public function update(MerchantReturnUpdate $update): void
    {
        // intentionally left empty
    }

    /**
     * @inheritDoc
     */
    public function createInShop(ReturnResponse $returnResponse): void
    {
        // intentionally left empty
    }
}
