<?php

namespace ChannelEngine\ChannelEngineIntegration\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks\ProductEventHandlerTask as BaseTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\Repository\ProductEventRepository;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProductEventHandlerTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\Tasks
 */
class ProductEventHandlerTask extends BaseTask
{
    /**
     * @return array
     *
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     */
    protected function getContexts(): array
    {
        return $this->getEventsRepository()->getContexts();
    }

    /**
     * @return ProductEventRepository
     *
     * @throws RepositoryNotRegisteredException
     */
    private function getEventsRepository(): ProductEventRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return RepositoryRegistry::getRepository(ProductEvent::getClassName());
    }
}
