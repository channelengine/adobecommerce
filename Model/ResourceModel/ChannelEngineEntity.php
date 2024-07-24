<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryCondition;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Utility\IndexHelper;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ChannelEngineEntity
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel
 */
class ChannelEngineEntity extends AbstractDb
{
    public const MAIN_TABLE = 'channel_engine_entity';
    public const ID_FIELD_NAME = 'id';
    /**
     * Resource model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }

    /**
     * @return array
     *
     * @throws LocalizedException
     */
    public function getContexts(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->where('type = ?', 'Configuration')
            ->from($this->getMainTable(), ['index_2'])
            ->distinct(true);

        $result = $connection->fetchAll($select);

        return array_filter(array_column($result, 'index_2'));
    }

    /**
     * Set resource model table name.
     *
     * @param string $tableName Name of the database table.
     */
    public function setTableName(string $tableName): void
    {
        $this->_init($tableName, 'id');
    }

    /**
     * Selects all records from ChannelEngine entity table.
     *
     * @return array ChannelEngine entity records.
     *
     * @throws LocalizedException
     */
    public function selectAll(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable());

        $result = $connection->fetchAll($select);

        return !empty($result) ? $result : [];
    }

    /**
     * Performs a select query over a specific type of entity with given ChannelEngine query filter.
     *
     * @param Entity $entity ChannelEngine entity.
     * @param QueryFilter|null $filter ChannelEngine query filter.
     *
     * @return array Array of selected records.
     *
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     */
    public function selectEntities(Entity $entity, QueryFilter $filter = null): array
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('type = ?', $entity->getConfig()->getType());

        if ($filter !== null) {
            $fieldIndexMap = IndexHelper::mapFieldsToIndexes($entity);

            if (!empty($filter->getConditions())) {
                $select->where($this->buildWhereCondition($filter, $fieldIndexMap));
            }

            if ($filter->getLimit()) {
                $select->limit($filter->getLimit(), $filter->getOffset());
            }

            $select = $this->buildOrderBy($select, $filter, $fieldIndexMap);
        }

        $result = $connection->fetchAll($select);

        return !empty($result) ? $result : [];
    }

    /**
     * Inserts a new record in ChannelEngine entity table.
     *
     * @param Entity $entity ChannelEngine entity.
     *
     * @return int ID of the inserted record.
     *
     * @throws LocalizedException
     */
    public function saveEntity(Entity $entity): int
    {
        $connection = $this->getConnection();
        $indexes = IndexHelper::transformFieldsToIndexes($entity);
        $data = $this->prepareDataForInsertOrUpdate($entity, $indexes);
        $data['type'] = $entity->getConfig()->getType();
        $connection->insert($this->getMainTable(), $data);

        $lastInsertId = $connection->lastInsertId($this->getMainTable());
        if (empty($lastInsertId)) {
            $select = $connection->select()
                ->from($this->getMainTable())
                ->order('id DESC')
                ->limit(1);
            $result = $connection->fetchAll($select);
            $lastInsertId = $result[0]['id'];
        }
        return (int) $lastInsertId;
    }

    /**
     * Updates an existing record in ChannelEngine entity table identified by ID.
     *
     * @param Entity $entity ChannelEngine entity.
     *
     * @return bool Returns TRUE if updateEntity has been successful, otherwise returns FALSE.
     *
     * @throws LocalizedException
     */
    public function updateEntity(Entity $entity): bool
    {
        $connection = $this->getConnection();

        $indexes = IndexHelper::transformFieldsToIndexes($entity);
        $data = $this->prepareDataForInsertOrUpdate($entity, $indexes);
        $whereCondition = [$this->getIdFieldName() . '=?' => $entity->getId()];

        $rows = $connection->update($this->getMainTable(), $data, $whereCondition);

        return $rows === 1;
    }

    /**
     * Deletes a record from ChannelEngine entity table.
     *
     * @param int $id ID of the record.
     *
     * @return bool Returns TRUE if updateEntity has been successful, otherwise returns FALSE.
     *
     * @throws LocalizedException
     */
    public function deleteEntity(int $id): bool
    {
        $connection = $this->getConnection();

        $rows = $connection->delete(
            $this->getMainTable(),
            [
                $connection->quoteInto('id = ?', $id),
            ]
        );

        return $rows === 1;
    }

    /**
     * Deletes entities identified by filter.
     *
     * @param QueryFilter $filter
     * @param Entity $entity
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     */
    public function deleteWhere(QueryFilter $filter, Entity $entity): void
    {
        $connection = $this->getConnection();
        $fieldIndexMap = IndexHelper::mapFieldsToIndexes($entity);

        $connection->delete(
            $this->getMainTable(),
            $this->buildWhereCondition($filter, $fieldIndexMap)
        );
    }

    /**
     * Prepares data for inserting a new record or updating an existing one.
     *
     * @param Entity $entity ChannelEngine entity object.
     * @param array $indexes Array of index values.
     *
     * @return array Prepared record for inserting or updating.
     */
    protected function prepareDataForInsertOrUpdate(Entity $entity, array $indexes): array
    {
        $record = ['data' => $this->serializeEntity($entity)];

        foreach ($indexes as $index => $value) {
            $record['index_' . $index] = $value;
        }

        return $record;
    }

    /**
     * Returns index mapped to given property.
     *
     * @param string $property Property name.
     * @param string $entityType Entity type.
     *
     * @return string Index column in ChannelEngine entity table.
     */
    protected function getIndexMapping(string $property, string $entityType): ?string
    {
        $indexMapping = IndexHelper::mapFieldsToIndexes(new $entityType);

        if (array_key_exists($property, $indexMapping)) {
            return 'index_' . $indexMapping[$property];
        }

        return null;
    }

    /**
     * Builds WHERE condition part of SELECT query.
     *
     * @param QueryFilter $filter ChannelEngine query filter.
     * @param array $fieldIndexMap Array of index mappings.
     *
     * @return string WHERE part of SELECT query.
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function buildWhereCondition(QueryFilter $filter, array $fieldIndexMap): string
    {
        $whereCondition = '';
        if ($filter->getConditions()) {
            foreach ($filter->getConditions() as $index => $condition) {
                if ($index !== 0) {
                    $whereCondition .= ' ' . $condition->getChainOperator() . ' ';
                }

                if ($condition->getColumn() === 'id') {
                    $whereCondition .= 'id = ' . $this->getConnection()->quote($condition->getValue());
                    continue;
                }

                if (!array_key_exists($condition->getColumn(), $fieldIndexMap)) {
                    throw new QueryFilterInvalidParamException(
                        sprintf('Field %s is not indexed!', $condition->getColumn())
                    );
                }

                $whereCondition .= $this->addCondition($condition, $fieldIndexMap);
            }
        }

        return $whereCondition;
    }

    /**
     * Filters records by given condition.
     *
     * @param QueryCondition $condition Query condition object.
     * @param array $indexMap Map of property indexes.
     *
     * @return string A single WHERE condition.
     */
    private function addCondition(QueryCondition $condition, array $indexMap): string
    {
        $column = $condition->getColumn();
        $columnName = $column === 'id' ? 'id' : 'index_' . $indexMap[$column];
        if ($column === 'id') {
            $conditionValue = (int)$condition->getValue();
        } else {
            $conditionValue = IndexHelper::castFieldValue($condition->getValue(), $condition->getValueType());
        }

        if (in_array($condition->getOperator(), [Operators::NOT_IN, Operators::IN], true)) {
            $values = array_map(function ($item) {
                if (is_string($item)) {
                    return "'$item'";
                }

                if (is_int($item)) {
                    $val = IndexHelper::castFieldValue($item, 'integer');
                    return "'{$val}'";
                }

                $val = IndexHelper::castFieldValue($item, 'double');

                return "'{$val}'";
            }, $condition->getValue());
            $conditionValue = '(' . implode(',', $values) . ')';
        } else {
            $conditionValue = "'$conditionValue'";
        }

        return $columnName . ' ' . $condition->getOperator()
            . (!in_array($condition->getOperator(), [Operators::NULL, Operators::NOT_NULL], true)
                ? $conditionValue : ''
            );
    }

    /**
     * Builds ORDER BY part of SELECT query.
     *
     * @param Select $select Magento SELECT query object.
     * @param QueryFilter $filter ChannelEngine query filter.
     * @param array $fieldIndexMap Array of index mappings.
     *
     * @return Select Updated Magento SELECT query object.
     *
     * @throws QueryFilterInvalidParamException
     */
    private function buildOrderBy(Select $select, QueryFilter $filter, array $fieldIndexMap): Select
    {
        $orderByColumn = $filter->getOrderByColumn();
        if ($orderByColumn) {
            $indexedColumn = null;
            if ($orderByColumn === 'id') {
                $indexedColumn = 'id';
            } elseif (array_key_exists($orderByColumn, $fieldIndexMap)) {
                $indexedColumn = 'index_' . $fieldIndexMap[$orderByColumn];
            }

            if ($indexedColumn === null) {
                throw new QueryFilterInvalidParamException(
                    sprintf('Unknown or not indexed OrderBy column %s', $orderByColumn)
                );
            }

            $select->order($indexedColumn . ' ' . $filter->getOrderDirection());
        }

        return $select;
    }

    /**
     * Serializes ChannelEngineEntity to string.
     *
     * @param Entity $entity ChannelEngineEntity object to be serialized
     *
     * @return string Serialized entity
     */
    private function serializeEntity(Entity $entity): string
    {
        return json_encode($entity->toArray());
    }
}
