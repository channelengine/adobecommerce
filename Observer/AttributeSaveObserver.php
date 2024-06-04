<?php

declare(strict_types=1);

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
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class AttributeSaveObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class AttributeSaveObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Initializer
     */
    private $initializer;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param ObjectManagerInterface $objectManager
     * @param Initializer $initializer
     */
    public function __construct(
        CollectionFactory      $productCollectionFactory,
        ObjectManagerInterface $objectManager,
        Initializer $initializer
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->objectManager = $objectManager;
        $this->initializer = $initializer;
    }

    /**
     * Handles attribute saved event.
     *
     * @param Observer $observer
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     * @throws RepositoryClassException
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        /** @var Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();

        if (!$attribute || $attribute->getEntityTypeId() !== $this->getProductAttributeTypeId()) {
            return;
        }

        $storeIds = $this->getConfigRepository()->getContexts();

        $productIds = $this->getProductIds($attribute);

        if ($productIds) {
            foreach ($storeIds as $storeId) {
                ConfigurationManager::getInstance()->setContext($storeId);

                if (!$this->getPluginStatusService()->isEnabled() || !$this->getStateService()->isOnboardingCompleted()) {
                    continue;
                }

                $handler = new ProductReplacedEventHandler();
                foreach ($productIds as $id) {
                    $handler->handle(new ProductReplaced($id));
                }
            }
        }
    }

    /**
     * @param Attribute $attribute
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getProductIds(Attribute $attribute): array
    {
        $storeId = $this->getStoreService()->getStoreId();

        $collection = $this->productCollectionFactory
            ->create()
            ->addAttributeToSelect($attribute->getAttributeCode())
            ->addFieldToFilter(
                [
                    ['attribute' => $attribute->getAttributeCode(), 'notnull' => true],
                    ['attribute' => $attribute->getAttributeCode(), 'neq' => ''],
                    ['attribute' => $attribute->getAttributeCode(), 'neq' => 'NO FIELD']
                ]
            )
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addFieldToFilter('type_id', ['in' => ['simple', 'bundle', 'grouped', 'configurable']])
            ->addStoreFilter($storeId);
        try {
            $ids = $collection->getAllIds();
        } catch (Exception $e) {
            $ids = [];
        }
        return $ids;
    }

    /**
     * @return int
     */
    private function getProductAttributeTypeId(): int
    {
        return $this->objectManager->create(
            Entity::class
        )->setType(
            Product::ENTITY
        )->getTypeId();
    }

    /**
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
    {
        return ServiceRegister::getService(PluginStatusService::class);
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
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
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }
}
