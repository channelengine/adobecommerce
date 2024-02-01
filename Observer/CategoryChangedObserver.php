<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductReplaced;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\ProductReplacedEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CategoryChangedObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class CategoryChangedObserver implements ObserverInterface
{
    private const GLOBAL_SCOPE = '0';

    /**
     * @var Initializer
     */
    private $initializer;

    public function __construct(Initializer $initializer)
    {
        $this->initializer = $initializer;
    }

    /**
     * Handles category changes.
     *
     * @param Observer $observer
     *
     * @return void
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException|RepositoryClassException
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        $category = $observer->getData('category');
        if (!$category) {
            return;
        }

        $storeId = $category->getData('store_id');
        $productIds = $category->getProductCollection()->addAttributeToSelect('entity_id')->getAllIds();

        if ($storeId === self::GLOBAL_SCOPE || $storeId === null) {
            $storeIds = $this->getConfigRepository()->getContexts();
            foreach ($storeIds as $id) {
                ConfigurationManager::getInstance()->setContext($id);

                if (!$this->getPluginStatusService()->isEnabled() || !$this->getStateService()->isOnboardingCompleted()) {
                    continue;
                }

                $this->saveProductUpdateEvent($productIds);
            }
        } else {
            ConfigurationManager::getInstance()->setContext($storeId);

            if (!$this->getPluginStatusService()->isEnabled() || !$this->getStateService()->isOnboardingCompleted()) {
                return;
            }

            $this->saveProductUpdateEvent($productIds);
        }
    }

    /**
     * @param array $productIds
     *
     * @return void
     */
    private function saveProductUpdateEvent(array $productIds): void
    {
        if ($productIds) {
            $handler = new ProductReplacedEventHandler();
            foreach ($productIds as $id) {
                $handler->handle(new ProductReplaced($id));
            }
        }
    }

    /**
     * @return BaseRepository
     *
     * @throws RepositoryNotRegisteredException
     */
    private function getConfigRepository(): BaseRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return RepositoryRegistry::getRepository(ConfigEntity::getClassName());
    }

    /**
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
    {
        return ServiceRegister::getService(PluginStatusService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }
}
