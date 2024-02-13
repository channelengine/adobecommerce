<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CategoryService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class CategoryService
{
    /**
     * @var Tree
     */
    private $categoryTree;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var array
     */
    private $resolvedCategories = [];

    /**
     * @param Tree $categoryTree
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Tree $categoryTree,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryTree = $categoryTree;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $categoryIds
     *
     * @return string
     *
     * @throws NoSuchEntityException
     * @throws QueryFilterInvalidParamException
     */
    public function getCategoryTrail(array $categoryIds): string
    {
        if (!$categoryIds) {
            return '';
        }

        $trails = [];

        foreach ($categoryIds as $categoryId) {
            $trails[] = $this->getTrail($categoryId);
        }

        return $trails ? implode(' > ', max($trails)) : '';
    }

    /**
     * @param string $categoryId
     *
     * @return array
     *
     * @throws NoSuchEntityException
     * @throws QueryFilterInvalidParamException
     */
    private function getTrail(string $categoryId): array
    {
        if (isset($this->resolvedCategories[$categoryId])) {
            return $this->resolvedCategories[$categoryId];
        }

        $storeId = $this->getStoreService()->getStoreId();
        $category = $this->categoryRepository->get($categoryId, $storeId);
        $categoryTree = $this->categoryTree->setStoreId($storeId)->loadBreadcrumbsArray($category->getPath());

        $categoryTrailArray = [];
        foreach ($categoryTree as $eachCategory) {
            $categoryTrailArray[] = $eachCategory['name'];
        }

        $this->resolvedCategories[$categoryId] = $categoryTrailArray;

        return $categoryTrailArray;
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }
}
