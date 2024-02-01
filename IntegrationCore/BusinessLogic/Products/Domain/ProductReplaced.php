<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain;

/**
 * Class ProductReplaced
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain
 */
class ProductReplaced
{
    /**
     * @var string
     */
    protected $id;

    /**
     * ProductDeleted constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return (string)$this->id;
    }
}