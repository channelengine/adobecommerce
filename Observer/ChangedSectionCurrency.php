<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Authorization\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\BootstrapComponent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ChangedSectionCurrency
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ChangedSectionCurrency implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var PluginStatusService
     */
    private $statusService;
    /**
     * @var StoreService
     */
    private $storeService;

    /**
     * @param  StoreManagerInterface  $storeManager
     * @param  PluginStatusService  $statusService
     * @param  StoreService  $storeService
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        PluginStatusService $statusService,
        StoreService $storeService
    ) {
        $this->storeManager = $storeManager;
        $this->statusService = $statusService;
        $this->storeService = $storeService;
    }

    /**
     * Handles currency changes.
     *
     * @param  Observer  $observer
     *
     * @return void
     * @throws NoSuchEntityException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException|\ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException
     */
    public function execute(Observer $observer): void
    {
        $this->init();

        $storeId = $observer->getData('store');
        if ($storeId === '') {
            $enabledStores = $this->getConfigRepository()->getContexts();

            foreach ($enabledStores as $enabledStore) {
                ConfigurationManager::getInstance()->setContext($enabledStore);
                try {
                    $store = $this->storeManager->getStore($enabledStore);
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                $config = $store->getConfig(Store::XML_PATH_PRICE_SCOPE);
                if ($config) {
                    continue;
                }

                if ($this->checkIfUnsupportedCurrency($store)) {
                    $this->statusService->disable();
                }
            }

            return;
        }

        ConfigurationManager::getInstance()->setContext($storeId);

        if ($storeId !== $this->storeService->getStoreId()) {
            return;
        }

        $store = $this->storeManager->getStore($storeId);

        if ($this->checkIfUnsupportedCurrency($store)) {
            $this->statusService->disable();
        }
    }

    /**
     * This event is triggered during installation, so we can't use an initializer due to the dependency
     * on an entity that is not available at that moment
     *
     * @throws \ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException
     */
    private function init(): void
    {
        BootstrapComponent::init();
        RepositoryRegistry::registerRepository(ConfigEntity::getClassName(), BaseRepository::getClassName());
    }

    /**
     * @param  StoreInterface  $store
     *
     * @return bool
     */
    private function checkIfUnsupportedCurrency(StoreInterface $store): bool
    {
        $authInfo = $this->getAuthProxy()->getAccountInfo();
        $currency = $store->getDefaultCurrencyCode();

        return $currency !== $authInfo->getCurrencyCode();
    }

    /**
     * @return Proxy
     */
    private function getAuthProxy(): Proxy
    {
        return ServiceRegister::getService(Proxy::class);
    }

    /**
     * @return BaseRepository
     * @throws RepositoryNotRegisteredException
     */
    private function getConfigRepository(): BaseRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return RepositoryRegistry::getRepository(ConfigEntity::getClassName());
    }
}
