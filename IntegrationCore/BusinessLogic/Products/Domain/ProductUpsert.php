<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain;

/**
 * Class ProductUpsert
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain
 */
class ProductUpsert
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var bool
     */
    protected $isVariant;
    /**
     * @var string | null
     */
    protected $parentId;

    /**
     * ProductUpsert constructor.
     * @param string $id
     * @param bool $isVariant
     * @param string|null $parentId
     */
    public function __construct($id, $isVariant = false, $parentId = null)
    {
        $this->id = $id;
        $this->isVariant = $isVariant;
        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return (string)$this->id;
    }

    /**
     * @return bool
     */
    public function isVariant()
    {
        return $this->isVariant;
    }

    /**
     * @return string|null
     */
    public function getParentId()
    {
        return $this->parentId ? (string)$this->parentId : null;
    }
}