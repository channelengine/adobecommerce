<?php

namespace ChannelEngine\ChannelEngineIntegration\Api;

use Magento\Inventory\Model\ResourceModel\Source\Collection as SourceCollection;

/**
 * Interface SourceCollectionFactoryInterface
 *
 * @package ChannelEngine\ChannelEngineIntegration\Api
 */
interface SourceCollectionFactoryInterface
{
    /**
     * Creates an instance of SourceCollection based on whether MSI is enabled or disabled.
     *
     * @return SourceCollection|null
     */
    public function create(): ?SourceCollection;
}
