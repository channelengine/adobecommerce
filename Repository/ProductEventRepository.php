<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Repository;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Repositories\ProductEventRepository as BaseEventRepository;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent as ProductEventEntity;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEventFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProductEventRepository
 *
 * @package ChannelEngine\ChannelEngineIntegration\Repository
 */
class ProductEventRepository extends BaseRepository implements BaseEventRepository
{
    /**
     * Fully qualified name of this class.
     */
    public const THIS_CLASS_NAME = __CLASS__;
    /**
     * Name of the base entity table in database.
     */
    public const TABLE_NAME = 'channel_engine_events';


    /**
     * ProductEventRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setResourceEntityObject($this->getProductEventFactory()->create());
        $this->getResourceEntityObject()->setTableName(self::TABLE_NAME);
    }

    /**
     * Returns resource entity.
     *
     * @return string Resource entity class name.
     */
    protected function getResourceEntity(): string
    {
        return ProductEvent::class;
    }

    /**
     * Retrieves all unique contexts from events table.
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getContexts(): array
    {
        return $this->getResourceEntityObject()->getContexts();
    }

    /**
     * Deletes multiple entities and returns success flag.
     *
     * @param ProductEventEntity[] $entities
     *
     * @return bool TRUE if operation succeeded; otherwise, FALSE.
     */
    public function batchDelete(array $entities): bool
    {
        return $this->getResourceEntityObject()->batchDelete($entities);
    }

    /**
     * @return ProductEventFactory
     */
    private function getProductEventFactory(): ProductEventFactory
    {
        return ServiceRegister::getService(ProductEventFactory::class);
    }
}
