<?php

namespace ChannelEngine\ChannelEngineIntegration\Command;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;

/**
 * class ConfigChangeCommand
 * @package ChannelEngine\ChannelEngineIntegration\Command
 */
class Init
{

    /**
     * @var Initializer
     */
    private $initializer;

    public function __construct(Initializer $initializer)
    {
        $this->initializer = $initializer;
    }

    /**
     * Initialize di for cli
     * @return void
     * @throws RepositoryClassException
     */
    public function init()
    {
        $this->initializer->init();
    }

}
