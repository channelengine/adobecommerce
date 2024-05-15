<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\Order\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as CoreFetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as CoreEntityFactory;
use Magento\Framework\Event\ManagerInterface as CoreEventManager;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as CoreSalesGrid;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\Order\Grid
 */
class Collection extends CoreSalesGrid
{
    /**
     * @param CoreEntityFactory $entityFactory
     * @param Logger $logger
     * @param CoreFetchStrategy $fetchStrategy
     * @param CoreEventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        CoreEntityFactory $entityFactory,
        Logger            $logger,
        CoreFetchStrategy $fetchStrategy,
        CoreEventManager  $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null): CoreSalesGrid
    {
        switch ($field) {
            case 'ce_channel_name': $field = 'channel_engine_order.channel_name'; break;
            case 'ce_channel_order_no': $field = 'channel_engine_order.channel_order_no'; break;
            case 'ce_channel_type_of_fulfillment': $field = 'channel_engine_order.channel_type_of_fulfillment';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @inheritDoc
     */
    protected function _renderFiltersBefore(): void
    {
        $joinTable = $this->getTable('channel_engine_order');
        $this->getSelect()->joinLeft(
            $joinTable,
            'main_table.entity_id = channel_engine_order.order_id',
            [
                'channel_name as ce_channel_name',
                'channel_order_no as ce_channel_order_no',
                'channel_type_of_fulfillment as ce_channel_type_of_fulfillment'
            ]
        );
        parent::_renderFiltersBefore();
    }
}
