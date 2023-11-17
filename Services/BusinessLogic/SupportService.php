<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole\SupportService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Exception;
use Magento\Framework\App\ResourceConnection;

/**
 * Class SupportService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class SupportService extends BaseService
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * SupportService constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resource = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    protected function hardReset(): void
    {
        try {
            $this->getWebhookService()->delete();
        } catch (Exception $e) {
            Logger::logError('Failed to delete webhook because: ' . $e->getMessage());
        }

        $this->deleteData();
    }

    /**
     * Removes data from ChannelEngine database tables.
     *
     * @return void
     */
    private function deleteData(): void
    {
        $this->truncate($this->getTableName('channel_engine_entity'));
        $this->truncate($this->getTableName('channel_engine_queue'));
        $this->truncate($this->getTableName('channel_engine_events'));
        $this->truncate($this->getTableName('channel_engine_logs'));
    }

    /**
     * @param string $tableName
     *
     * @return void
     */
    private function truncate(string $tableName): void
    {
        $connection = $this->resource->getConnection();
        $sql = "truncate " . $tableName . ";";
        $connection->query($sql);
    }

    /**
     * @param string $table
     *
     * @return string
     */
    private function getTableName(string $table): string
    {
        return $this->resource->getTableName($table);
    }

    /**
     * @return WebhooksService
     */
    private function getWebhookService(): WebhooksService
    {
        return ServiceRegister::getService(WebhooksService::class);
    }
}
