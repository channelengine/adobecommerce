<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use ChannelEngine\ChannelEngineIntegration\Api\ReturnsServiceFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\ReturnsService as BaseReturnsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\NullReturnsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ReturnsService;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class ReturnsServiceFactory
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class ReturnsServiceFactory implements ReturnsServiceFactoryInterface
{
    /**
     * Creates an instance of ReturnsService based on shop edition.
     *
     * @return ReturnsService
     */
    public function create(): BaseReturnsService
    {
        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = ObjectManager::getInstance()->get(ProductMetadataInterface::class);
        $edition = $productMetadata->getEdition();
        if ($edition === 'Enterprise') {
            return ObjectManager::getInstance()->get(ReturnsService::class);
        }

        return ObjectManager::getInstance()->get(NullReturnsService::class);
    }
}
