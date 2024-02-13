<?php

namespace ChannelEngine\ChannelEngineIntegration\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductsUpsertTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\SalesPricesService;

/**
 * Class CheckSalePricesTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\Tasks
 */
class CheckSalePricesTask extends Task
{
    /**
     * @inheritDoc
     *
     * @throws QueueStorageUnavailableException
     * @throws QueryFilterInvalidParamException
     */
    public function execute(): void
    {
        if (!$this->shouldSync()) {
            $this->reportProgress(100);

            return;
        }

        $productIds = $this->getSalesPricesService()->getProductsWithSalesPricesIds();
        $this->reportProgress(30);

        if (empty($productIds)) {
            $this->reportProgress(100);

            return;
        }

        $this->getQueueService()->enqueue(
            'channel-engine-sales-products-sync',
            new ProductsUpsertTask($productIds),
            ConfigurationManager::getInstance()->getContext()
        );
        $this->reportProgress(100);
    }

    /**
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function shouldSync(): bool
    {
        $priceSettings = $this->getPriceSettingsService()->getPriceSettings();

        return $priceSettings || $priceSettings->getPriceAttribute() === 'FINAL_PRICE';
    }

    /**
     * @return SalesPricesService
     */
    private function getSalesPricesService(): SalesPricesService
    {
        return ServiceRegister::getService(SalesPricesService::class);
    }

    /**
     * @return QueueService
     */
    private function getQueueService(): QueueService
    {
        return ServiceRegister::getService(QueueService::class);
    }

    /**
     * @return PriceSettingsService
     */
    private function getPriceSettingsService(): PriceSettingsService
    {
        return ServiceRegister::getService(PriceSettingsService::class);
    }
}
