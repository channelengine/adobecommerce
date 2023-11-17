<?php

namespace ChannelEngine\ChannelEngineIntegration\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ChannelEngineOrder
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model\ResourceModel
 */
class ChannelEngineOrder extends AbstractDb
{
    public const MAIN_TABLE = 'channel_engine_order';
    public const ID_FIELD_NAME = 'id';

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }

    /**
     * @param string $channelOrderNo
     * @param string $orderId
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getOrder(string $channelOrderNo, string $orderId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('order_id = ?', $orderId)
            ->where('channel_order_no = ?', $channelOrderNo);

        $result = $connection->fetchAll($select);

        return !empty($result) ? $result : [];
    }

    /**
     * @param string $channelOrderNo
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getOrderByChannelOrderNo(string $channelOrderNo): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('channel_order_no = ?', $channelOrderNo);

        $result = $connection->fetchAll($select);

        return !empty($result) ? $result : [];
    }
}
