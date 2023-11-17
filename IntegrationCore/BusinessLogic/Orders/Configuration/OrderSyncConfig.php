<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;

/**
 * Class OrderSyncConfig
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration
 */
class OrderSyncConfig extends Entity
{
    const CLASS_NAME = __CLASS__;

    protected $fields = [
        'id',
        'incomingOrders',
        'shippedOrders',
        'fulfilledOrders',
        'enableShipmentInfoSync',
        'enableOrderCancellationSync',
        'enableOrdersByMerchantSync',
        'enableOrdersByMarketplaceSync',
        'enableReduceStock',
        'unknownLinesHandling',
        'fromDate',
        'enableReturnsSync'
    ];
    /**
     * @var string
     */
    protected $incomingOrders;
    /**
     * @var string
     */
    protected $shippedOrders;
    /**
     * @var string
     */
    protected $fulfilledOrders;
    /**
     * @var bool
     */
    protected $enableShipmentInfoSync;
    /**
     * @var bool
     */
    protected $enableOrderCancellationSync;
    /**
     * @var bool
     */
    protected $enableOrdersByMerchantSync;
    /**
     * @var bool
     */
    protected $enableOrdersByMarketplaceSync;
    /**
     * @var bool 
     */
    protected $enableReduceStock;
    /**
     * @var string
     */
    protected $unknownLinesHandling;
    /**
     * @var string
     */
    protected $fromDate;
    /**
     * @var bool
     */
    protected $enableReturnsSync;

    /**
     * @return string
     */
    public function getIncomingOrders()
    {
        return $this->incomingOrders;
    }

    /**
     * @param string $incomingOrders
     */
    public function setIncomingOrders($incomingOrders)
    {
        $this->incomingOrders = $incomingOrders;
    }

    /**
     * @return string
     */
    public function getShippedOrders()
    {
        return $this->shippedOrders;
    }

    /**
     * @param string $shippedOrders
     */
    public function setShippedOrders($shippedOrders)
    {
        $this->shippedOrders = $shippedOrders;
    }

    /**
     * @return string
     */
    public function getFulfilledOrders()
    {
        return $this->fulfilledOrders;
    }

    /**
     * @param string $fulfilledOrders
     */
    public function setFulfilledOrders($fulfilledOrders)
    {
        $this->fulfilledOrders = $fulfilledOrders;
    }

	/**
	 * @return bool
	 */
	public function isEnableShipmentInfoSync() {
		return $this->enableShipmentInfoSync;
	}

	/**
	 * @param bool $enableShipmentInfoSync
	 */
	public function setEnableShipmentInfoSync($enableShipmentInfoSync ) {
		$this->enableShipmentInfoSync = $enableShipmentInfoSync;
	}

	/**
	 * @return bool
	 */
	public function isEnableOrderCancellationSync() {
		return $this->enableOrderCancellationSync;
	}

	/**
	 * @param bool $enableOrderCancellationSync
	 */
	public function setEnableOrderCancellationSync( $enableOrderCancellationSync ) {
		$this->enableOrderCancellationSync = $enableOrderCancellationSync;
	}

	/**
	 * @return bool
	 */
	public function isEnableOrdersByMerchantSync() {
		return $this->enableOrdersByMerchantSync;
	}

	/**
	 * @param bool $enableOrdersByMerchantSync
	 */
	public function setEnableOrdersByMerchantSync( $enableOrdersByMerchantSync ) {
		$this->enableOrdersByMerchantSync = $enableOrdersByMerchantSync;
	}

	/**
	 * @return bool
	 */
	public function isEnableOrdersByMarketplaceSync() {
		return $this->enableOrdersByMarketplaceSync;
	}

	/**
	 * @param bool $enableOrdersByMarketplaceSync
	 */
	public function setEnableOrdersByMarketplaceSync( $enableOrdersByMarketplaceSync ) {
		$this->enableOrdersByMarketplaceSync = $enableOrdersByMarketplaceSync;
	}

	/**
	 * @return bool
	 */
	public function isEnableReduceStock() {
		return $this->enableReduceStock;
	}

    /**
     * @param bool $enableReduceStock
     */
    public function setEnableReduceStock( $enableReduceStock ) {
        $this->enableReduceStock = $enableReduceStock;
    }

    /**
     * @return string
     */
    public function getUnknownLinesHandling() {
        return $this->unknownLinesHandling;
    }

    /**
     * @param string $unknownLinesHandling
     */
    public function setUnknownLinesHandling( $unknownLinesHandling ) {
        $this->unknownLinesHandling = $unknownLinesHandling;
    }

    /**
     * @return string
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param string $fromDate
     */
    public function setFromDate( $fromDate )
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return bool
     */
    public function isEnableReturnsSync()
    {
        return $this->enableReturnsSync;
    }

    /**
     * @param bool $enableReturnsSync
     */
    public function setEnableReturnsSync($enableReturnsSync)
    {
        $this->enableReturnsSync = $enableReturnsSync;
    }

    public function getConfig()
    {
        return new EntityConfiguration(new IndexMap(), 'OrderSyncConfig');
    }
}