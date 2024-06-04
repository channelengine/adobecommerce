<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Listeners\Products;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueService;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\InitialSyncStateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products\ProductSalePricesService;
use ChannelEngine\ChannelEngineIntegration\Tasks\CheckSalePricesTask;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SalePricesListener
 *
 * @package ChannelEngine\ChannelEngineIntegration\Listeners\Products
 */
class SalePricesListener
{
    use SetsContextTrait;

    /**
     * Time when task should be enqueued.
     */
    public const CHECK_TIME = 'today midnight';
    /**
     * Interval between two executions of task.
     */
    public const CHECK_PERIOD = 86400;

    /**
     * Every day at midnight task for checking if there are products that are on sale will be enqueued.
     *
     * @throws QueueStorageUnavailableException
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     * @throws ContextNotSetException
     */
    public function handle(): void
    {
        $storesIds = $this->getConfigRepository()->getContexts();
        foreach ($storesIds as $storeId) {
            $this->setContextWithStoreId($storeId);
            if ($this->getInitialSyncStateService()->checkInitialSyncState(InitialSyncStateService::FINISHED)
                && time() > $this->getService()->getLastReadTime() + $this::CHECK_PERIOD) {
                $this->getQueue()->enqueue('channel-engine-check-sale-prices', new CheckSalePricesTask(), $storeId);
                $this->getService()->updateLastReadTime(strtotime($this::CHECK_TIME));
            }
        }
    }

    /**
     * @return ProductSalePricesService
     */
    private function getService(): ProductSalePricesService
    {
        return ServiceRegister::getService(ProductSalePricesService::class);
    }

    /**
     * @return InitialSyncStateService
     */
    private function getInitialSyncStateService(): InitialSyncStateService
    {
        return ServiceRegister::getService(InitialSyncStateService::class);
    }

    /**
     * @return QueueService
     */
    private function getQueue(): QueueService
    {
        return ServiceRegister::getService(QueueService::CLASS_NAME);
    }

    /**
     * @return BaseRepository
     *
     * @throws RepositoryNotRegisteredException
     */
    private function getConfigRepository(): BaseRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return RepositoryRegistry::getRepository(ConfigEntity::getClassName());
    }
}
