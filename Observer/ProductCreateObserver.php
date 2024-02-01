<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductDeleted;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductReplaced;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductUpsert;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\ProductDeletedEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\ProductReplacedEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\ProductUpsertEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class ProductCreateObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ProductCreateObserver implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Initializer
     */
    private $initializer;
    /**
     * @var Configurable
     */
    protected $configurableType;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Configurable $configurableType
     * @param ProductRepository $productRepository
     * @param Initializer $initializer
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Configurable $configurableType,
        ProductRepository $productRepository,
        Initializer $initializer
    )
    {
        $this->storeManager = $storeManager;
        $this->configurableType = $configurableType;
        $this->productRepository = $productRepository;
        $this->initializer = $initializer;
    }

    /**
     * Handles product create event.
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

        $product = $observer->getData('product');

        if (!$product || in_array($product->getTypeId(), ['bundle', 'grouped'])) {
            return;
        }

        $storeId = (string)$product->getData('store_id');

        if ($storeId === '0') {
            $storesIds = $this->getConfigRepository()->getContexts();
            $websiteIds = $product->getData('website_ids');

            $websites = [];

            foreach ($websiteIds as $id) {
                $websites[] = $this->storeManager->getWebsite($id);
            }

            $stores = [];

            foreach ($websites as $website) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $stores = array_merge($stores, $website->getStoreIds());
            }

            foreach ($storesIds as $storeId) {
                if (in_array($storeId, $stores, true)) {
                    $this->saveProductEvent($storeId, $product);
                }
            }

            return;
        }

        $this->saveProductEvent($storeId, $product);
    }

    /**
     * @param string $storeId
     * @param Product $product
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function saveProductEvent(string $storeId, Product $product): void
    {
        ConfigurationManager::getInstance()->setContext($storeId);

        if (!$this->getPluginStatusService()->isEnabled() || !$this->getStateService()->isOnboardingCompleted()) {
            return;
        }

        $productId = $product->getId();
        $parentIds = $this->configurableType->getParentIdsByChild($productId);
        if ($parentIds) {
            $product = $this->productRepository->getById($parentIds[0]);
        }

        if (!in_array($product->getTypeId(), ['simple', 'bundle', 'grouped', 'configurable'], true)) {
            return;
        }

        $mappings = $this->getMappingsService()->getAttributeMappings();
        $productId = ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID) ?
            $product->getId() : $product->getSku();

        if (!$productId) {
            return;
        }

        if ((int)$product->getStatus() === Status::STATUS_DISABLED) {
            $handler = new ProductDeletedEventHandler();
            $handler->handle(new ProductDeleted($productId));

            return;
        }

        if (ProductCreateBeforeObserver::isProductCreated()) {
            $handler = new ProductUpsertEventHandler();
            $handler->handle(new ProductUpsert($product->getId()));
        } else {
            $handler = new ProductReplacedEventHandler();
            $handler->handle(new ProductReplaced($product->getId()));
        }

        if ($product->getTypeId() === 'configurable') {
            $variantsBefore = ProductCreateBeforeObserver::getVariantsBefore();
            $variantsNew = $product->getExtensionAttributes()->getConfigurableProductLinks();

            $newStandaloneProductIds = array_diff($variantsBefore, $variantsNew);

            $handler = new ProductUpsertEventHandler();
            foreach ($newStandaloneProductIds as $newStandaloneProductId) {
                $handler->handle(new ProductUpsert($newStandaloneProductId));
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

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }
}
