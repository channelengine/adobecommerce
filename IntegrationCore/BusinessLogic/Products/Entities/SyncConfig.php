<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;

class SyncConfig extends Entity
{
    const CLASS_NAME = __CLASS__;

    protected $fields = ['id', 'language', 'priceField', 'defaultStock', 'enabledStockSync'];

    /**
     * @var string
     */
    protected $language;
    /**
     * @var string;
     */
    protected $priceField;
    /**
     * @var int
     */
    protected $defaultStock;
	/**
	 * @var boolean
	 */
	protected $enabledStockSync;

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getPriceField()
    {
        return $this->priceField;
    }

    /**
     * @param string $priceField
     */
    public function setPriceField($priceField)
    {
        $this->priceField = $priceField;
    }

    /**
     * @return int
     */
    public function getDefaultStock()
    {
        return $this->defaultStock;
    }

    /**
     * @param int $defaultStock
     */
    public function setDefaultStock($defaultStock)
    {
        $this->defaultStock = $defaultStock;
    }

	/**
	 * @return bool
	 */
	public function isEnabledStockSync()
	{
		return $this->enabledStockSync;
	}

	/**
	 * @param bool $enabledStockSync
	 */
	public function setEnabledStockSync($enabledStockSync)
	{
		$this->enabledStockSync = $enabledStockSync;
	}

    public function getConfig()
    {
        return new EntityConfiguration(new IndexMap(), 'SyncConfig');
    }
}