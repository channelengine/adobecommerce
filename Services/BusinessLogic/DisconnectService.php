<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class DisconnectService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class DisconnectService
{
    /**
     * @var ResourceConnection
     */
    private $resource;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ResourceConnection $resource
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ResourceConnection $resource, ProductMetadataInterface $productMetadata)
    {
        $this->resource = $resource;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return void
     *
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     */
    public function disconnect(): void
    {
        try {
            $this->getWebhookService()->delete();
        } catch (BaseException $e) {
            Logger::logError('Failed to delete webhook because ' . $e->getMessage());
        }

        if (count($this->getConfigRepository()->getContexts()) === 1) {
            $this->truncateOrderData();
        }

        $this->removeTable('channel_engine_entity', 'index_2');
        $this->removeTable('channel_engine_queue', 'index_4');
        $this->removeTable('channel_engine_logs', 'index_2');
        $this->removeTable('channel_engine_events', 'context');

        if ($this->productMetadata->getEdition() === 'Enterprise') {
            $this->removeTable('channel_engine_returns', 'index_2');
        }
    }

    /**
     * @param string $tableName
     * @param string $column
     *
     * @return void
     */
    private function removeTable(string $tableName, string $column): void
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName($tableName);
        $connection->delete($tableName, [$connection->quoteInto($column . ' = ?', ConfigurationManager::getInstance()->getContext())]);
    }

    /**
     * @return void
     */
    private function truncateOrderData(): void
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('channel_engine_order');
        $connection->truncateTable($tableName);
    }

    /**
     * @return WebhooksService
     */
    private function getWebhookService(): WebhooksService
    {
        return ServiceRegister::getService(WebhooksService::class);
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
