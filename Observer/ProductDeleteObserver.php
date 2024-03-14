<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductPurged;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\ProductReplaced;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\ProductPurgedEventHandler;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Handlers\ProductReplacedEventHandler;
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
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class ProductDeleteObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ProductDeleteObserver implements ObserverInterface
{
    /**
     * @var Initializer
     */
    private $initializer;
    /**
     * @var Configurable
     */
    private $configurableType;

    /**
     * @param Initializer $initializer
     * @param Configurable $configurableType
     */
    public function __construct(
        Configurable $configurableType,
        Initializer $initializer
    )
    {
        $this->initializer = $initializer;
        $this->configurableType = $configurableType;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryClassException
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        $product = $observer->getData('product');

        if (!$product || in_array($product->getTypeId(), ['bundle', 'grouped'])) {
            return;
        }

        $storeIds = $this->getConfigRepository()->getContexts();
        foreach ($storeIds as $storeId) {
            $this->saveProductDeleteEvent($storeId, $product);
        }
    }

    /**
     * @param string $storeId
     * @param Product $product
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    private function saveProductDeleteEvent(string $storeId, Product $product): void
    {
        ConfigurationManager::getInstance()->setContext($storeId);

        if (!$this->getPluginStatusService()->isEnabled() || !$this->getStateService()->isOnboardingCompleted()) {
            return;
        }

        $mappings = $this->getMappingsService()->getAttributeMappings();
        $productId = ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID) ?
            $product->getId() : $product->getSku();

        $childProductIds = [];
        $parentProductIds = [];
        if ($product->getTypeId() === 'configurable') {
            $configurableProduct = $product->getTypeInstance();
            $childProducts = $configurableProduct->getUsedProducts($product);

            foreach ($childProducts as $childProduct) {
                $childProductIds[] = $childProduct->getId();
            }
        } else if ($product->getTypeId() === 'virtual' || $product->getTypeId() === 'simple') {
            $parentProductIds = $this->configurableType->getParentIdsByChild($product->getId());
        }

        $handler = new ProductPurgedEventHandler();
        $handler->handle(new ProductPurged($productId));

        $toSyncProductIds = $childProductIds ?: ($parentProductIds ?: []);
        foreach ($toSyncProductIds as $toSyncProductId) {
            $handler = new ProductReplacedEventHandler();
            $handler->handle(new ProductReplaced($toSyncProductId));
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
