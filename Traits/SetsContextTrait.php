<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Traits;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use Magento\Framework\App\RequestInterface;

trait SetsContextTrait
{
    /**
     * Sets context.
     *
     * @param RequestInterface $request
     *
     * @return void
     *
     * @throws ContextNotSetException
     */
    public function setContext(RequestInterface $request): void
    {
        $storeId = $request->getParam('storeId');

        if (empty($storeId)) {
            throw new ContextNotSetException('Store id is not set.');
        }

        ConfigurationManager::getInstance()->setContext($storeId);
    }

    /**
     * Sets context with store ID.
     *
     * @param string $storeId
     *
     * @return void
     *
     * @throws ContextNotSetException
     */
    public function setContextWithStoreId(string $storeId): void
    {
        if (empty($storeId)) {
            throw new ContextNotSetException('Store id is not set.');
        }

        ConfigurationManager::getInstance()->setContext($storeId);
    }
}
