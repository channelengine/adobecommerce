<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use Magento\Framework\Exception\LocalizedException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent as ProductEventEntity;

/**
 * Class ProductEvent
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel
 */
class ProductEvent extends ChannelEngineEntity
{
    /**
     * @inheritDoc
     */
    public function selectEntities(Entity $entity, QueryFilter $filter = null): array
    {
        if (!$filter) {
            $filter = new QueryFilter();
        }

        return parent::selectEntities($entity, $filter);
    }

    /**
     * Deletes multiple entities and returns success flag.
     *
     * @param ProductEventEntity[] $entities
     *
     * @return bool
     *
     * @throws LocalizedException
     */
    public function batchDelete(array $entities): bool
    {
        $ids = [];

        foreach ($entities as $entity) {
            $ids[] = $entity->getId();
        }

        $eventIds = implode(', ', $ids);

        $connection = $this->getConnection();
        $rows = $connection->delete(
            $this->getMainTable(),
            "id IN ($eventIds)"
        );

        return $rows === count($ids);
    }

    /**
     * Retrieves all available contexts.
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getContexts(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), ['context'])
            ->distinct(true);

        $result = $connection->fetchAll($select);

        return array_column($result, 'context');
    }

    /**
     * @inheritDoc
     */
    protected function buildWhereCondition(QueryFilter $filter, array $fieldIndexMap): string
    {
        $whereConditions = parent::buildWhereCondition($filter, $fieldIndexMap);

        if ($filter->getConditions()) {
            $whereConditions .= 'AND context=' . ConfigurationManager::getInstance()->getContext();
        } else {
            $whereConditions .= ' context=' . ConfigurationManager::getInstance()->getContext();
        }

        return $whereConditions;
    }

    /**
     * @inheritDoc
     */
    protected function prepareDataForInsertOrUpdate(Entity $entity, array $indexes): array
    {
        $record = parent::prepareDataForInsertOrUpdate($entity, $indexes);
        $record['context'] = ConfigurationManager::getInstance()->getContext();

        return $record;
    }
}
