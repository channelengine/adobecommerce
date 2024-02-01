<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductDeleted;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductPurged;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductReplaced;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductUpsert;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent;

/**
 * Interface ProductEventsBufferService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts
 */
interface ProductEventsBufferService
{
    /**
     * Records deleted product event.
     *
     * @param ProductDeleted $deleted
     *
     * @return void
     */
    public function recordDeleted(ProductDeleted $deleted);

    /**
     * Records product upsert event
     *
     * @param ProductUpsert $upsert
     *
     * @return void
     */
    public function recordUpsert(ProductUpsert $upsert);

    /**
     * Records replaced product event.
     *
     * @param ProductReplaced $replaced
     *
     * @return void
     */
    public function recordReplaced(ProductReplaced $replaced);

    /**
     * Records product purged event
     *
     * @param ProductPurged $purged
     *
     * @return void
     */
    public function recordPurged(ProductPurged $purged);

    /**
     * Gets events with given type.
     *
     * @param string $type
     * @param int $offset
     * @param int $limit
     *
     * @return ProductEvent[]
     */
    public function get($type, $offset, $limit);

    /**
     * Deletes product events.
     *
     * @param ProductEvent[] $events
     *
     * @return void
     */
    public function delete(array $events);

    /**
     * Retrieves last time events from buffer were read.
     *
     * @return int
     */
    public function getLastReadTime();

    /**
     * Updates last time events from buffer were read.
     *
     * @param int $lastReadTime
     *
     * @return void
     */
    public function updateLastReadTime($lastReadTime);
}