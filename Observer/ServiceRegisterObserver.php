<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ServiceRegisterObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ServiceRegisterObserver implements ObserverInterface
{
    /**
     * @var Initializer
     */
    private $initializer;

    /**
     * ServiceRegisterObserver constructor.
     *
     * @param Initializer $initializer
     */
    public function __construct(Initializer $initializer)
    {
        $this->initializer = $initializer;
    }

    /**
     * Register all needed services.
     *
     * @param Observer $observer
     *
     * @return void
     *
     * @throws RepositoryClassException
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();
    }
}
