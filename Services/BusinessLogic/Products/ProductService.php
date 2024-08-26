<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class ProductService implements ProductsService
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Configurable
     */
    private $configurableType;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param  CollectionFactory  $collectionFactory
     * @param  ProductRepository  $productRepository
     * @param  SearchCriteriaBuilder  $searchCriteriaBuilder
     * @param  Configurable  $configurableType
     * @param  ProductFactory  $productFactory
     * @param  \Magento\Store\Model\StoreManagerInterface  $storeManage
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Configurable $configurableType,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManage
    ) {
        $this->collectionFactory     = $collectionFactory;
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configurableType      = $configurableType;
        $this->productFactory        = $productFactory;
        $this->storeManager          = $storeManage;
    }

    /**
     * @inheritDoc
     * @throws QueryFilterInvalidParamException
     */
    public function getProductIds($page, $limit = 5000): array
    {
        $storeId    = $this->getStoreService()->getStoreId();
        /** @var Collection $collection */
        $collection = $this->collectionFactory
            ->create()
            ->setPage($page, $limit)
            ->addStoreFilter($storeId)
            ->addFieldToFilter('type_id', ['in' => ['simple', 'bundle', 'grouped', 'configurable']]);

        return $collection->getAllIds($limit, $page * $limit);
    }

    /**
     * Retrieves total number of products.
     *
     * @return int
     * @throws QueryFilterInvalidParamException
     */
    public function count(): int
    {
        $storeId    = $this->getStoreService()->getStoreId();
        $collection = $this->collectionFactory
            ->create()
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addFieldToFilter('type_id', ['in' => ['simple', 'bundle', 'grouped', 'configurable']]);

        return $collection->count();
    }

    /**
     * @inheritDoc
     * @throws QueryFilterInvalidParamException
     * @throws LocalizedException
     */
    public function getProducts(array $ids): array
    {
        $storeId = $this->getStoreService()->getStoreId();
        $this->storeManager->setCurrentStore($storeId);
        $this->searchCriteriaBuilder->addFilter('entity_id', $ids, 'in');
        $criteria = $this->searchCriteriaBuilder->create();
        if (count($ids) === 1 && array_key_exists(0, $ids)) {
            $products[] = $this->productRepository->getById($ids[0], false, $storeId);
        } else {
            $products = $this->productRepository->getList($criteria)->getItems();
        }

        $ceProducts = [];

        /** @var Product $product */
        foreach ($products as $product) {
            if( (int)$product->getStatus() !== Status::STATUS_ENABLED ) {
                continue;
            }

            $parentIds = $this->configurableType->getParentIdsByChild($product->getId());
            if ($parentIds) {
                $parentId = $parentIds[0];
                $product  = $this->productRepository->getById($parentId);
            }

            $ceProducts[] = $this->productFactory->getProduct($product);
        }

        return $ceProducts;
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }
}
