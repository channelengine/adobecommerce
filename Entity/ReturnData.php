<?php

namespace ChannelEngine\ChannelEngineIntegration\Entity;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;

/**
 * Class ReturnData
 *
 * @package ChannelEngine\ChannelEngineIntegration\Entity
 */
class ReturnData extends Entity
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * @var string
     */
    protected $rmaId;
    /**
     * @var int
     */
    protected $returnId;
    /**
     * @var string
     */
    protected $context;
    /**
     * Array of field names.
     *
     * @var array
     */
    protected $fields = ['id', 'rmaId', 'returnId', 'context'];

    /**
     * @return string
     */
    public function getRmaId(): string
    {
        return $this->rmaId;
    }

    /**
     * @param string $rmaId
     */
    public function setRmaId(string $rmaId): void
    {
        $this->rmaId = $rmaId;
    }

    /**
     * @return int
     */
    public function getReturnId(): int
    {
        return $this->returnId;
    }

    /**
     * @param int $returnId
     */
    public function setReturnId(int $returnId): void
    {
        $this->returnId = $returnId;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $map = new IndexMap();
        $map->addIntegerIndex('rmaId')
            ->addStringIndex('context');

        return new EntityConfiguration($map, 'ReturnData');
    }
}
