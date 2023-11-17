<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\TickEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task;

/**
 * Class ProductEventHandlerTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks
 */
class ProductEventHandlerTask extends Task
{
    /**
     * @var array
     */
    protected $contexts;
    /**
     * @var array
     */
    protected $processedContexts;
    /**
     * @var AuthorizationService
     */
    protected $authService;

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'contexts' => $this->contexts,
            'processedContexts' => $this->processedContexts,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        return new static($array['contexts'], $array['processedContexts']);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return Serializer::serialize($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $unserialized = Serializer::unserialize($serialized);

        $this->contexts = $unserialized['contexts'];
        $this->processedContexts = $unserialized['processedContexts'];
    }

    /**
     * @param array $contexts
     * @param array $processedContexts
     */
    public function __construct(array $contexts = [], array $processedContexts = [])
    {
        $this->contexts = $contexts;
        $this->processedContexts = $processedContexts;
    }

    /**
     * @inheritDoc
     *
     * @throws QueueStorageUnavailableException
     */
    public function execute()
    {
        $this->contexts = $this->getContexts();
        foreach ($this->contexts as $context) {
            if (in_array($context, $this->processedContexts, true)) {
                continue;
            }

            ConfigurationManager::getInstance()->setContext($context);

            if ($this->isAuthorized()) {
                $handler = new TickEventHandler();
                $handler->handle();
            }

            $this->processedContexts[] = $context;
            $this->reportProgress($this->getCurrentProgress());
        }

        $this->reportProgress(100);
    }

    /**
     * @return bool
     */
    protected function isAuthorized()
    {
        try {
            $authInfo = $this->getAuthService()->getAuthInfo();

            return $authInfo !== null;
        } catch (BaseException $e) {
            return false;
        }
    }

    /**
     * @return float
     */
    protected function getCurrentProgress()
    {
        return (count($this->processedContexts) * 95.0) / count($this->contexts);
    }

    /**
     * @return array
     */
    protected function getContexts()
    {
        return [''];
    }

    /**
     * @return AuthorizationService
     */
    protected function getAuthService()
    {
        if ($this->authService === null) {
            $this->authService = ServiceRegister::getService(AuthorizationService::class);
        }

        return $this->authService;
    }
}