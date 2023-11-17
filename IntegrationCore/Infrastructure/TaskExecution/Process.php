<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\EntityConfiguration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Configuration\IndexMap;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\Runnable;
use InvalidArgumentException;

/**
 * Class Process
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Entities
 */
class Process extends Entity
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Unique identifier.
     *
     * @var string
     */
    protected $guid;
    /**
     * Runnable instance.
     *
     * @var Runnable
     */
    protected $runner;

    /**
     * Sets raw array data to this entity instance properties.
     *
     * @param array $data Raw array data with keys 'id', 'guid' and 'runner'.
     *
     * @throws InvalidArgumentException In case when @see $data does not have all needed keys.
     */
    public function inflate(array $data)
    {
        if (!isset($data['guid'], $data['runner'])) {
            throw new InvalidArgumentException('Data array needs to have "guid" and "runner" keys.');
        }

        parent::inflate($data);
        $this->setGuid($data['guid']);
        $this->setRunner(Serializer::unserialize($data['runner']));
    }

    /**
     * Transforms entity to its array format representation.
     *
     * @return array Entity in array format.
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data['guid'] = $this->getGuid();
        $data['runner'] = Serializer::serialize($this->getRunner());

        return $data;
    }

    /**
     * Returns entity configuration object
     *
     * @return EntityConfiguration
     */
    public function getConfig()
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('guid');

        return new EntityConfiguration($indexMap, 'Process');
    }

    /**
     * Gets Guid.
     *
     * @return string Guid.
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Sets Guid.
     *
     * @param string $guid Guid.
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * Gets Runner.
     *
     * @return Runnable Runner.
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Sets Runner.
     *
     * @param Runnable $runner Runner.
     */
    public function setRunner(Runnable $runner)
    {
        $this->runner = $runner;
    }
}
