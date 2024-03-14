<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;

/**
 * Class ProductSalePricesService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class ProductSalePricesService
{
    /**
     * Get last time when sale prices were checked.
     *
     * @return int
     */
    public function getLastReadTime(): int
    {
        return $this->getConfigService()->getLastSalePriceCheckTime();
    }

    /**
     * Set last time when sale prices were checked.
     *
     * @param int $lastReadTime
     */
    public function updateLastReadTime(int $lastReadTime): void
    {
        $this->getConfigService()->setLastSalePriceCheckTime($lastReadTime);
    }

    /**
     * Retrieves instance of Configuration.
     *
     * @return Configuration
     */
    private function getConfigService(): Configuration
    {
        return ServiceRegister::getService(Configuration::class);
    }
}
