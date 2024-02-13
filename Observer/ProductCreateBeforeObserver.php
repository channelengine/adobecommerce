<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class ProductCreateObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ProductCreateBeforeObserver implements ObserverInterface
{
    /**
     * @var bool
     */
    private static $isCreated = false;
    /**
     * @var array
     */
    private static $variantsBefore = [];

    /**
     * Handles product create event.
     *
     * @param Observer $observer
     *
     * @return void
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     */
    public function execute(Observer $observer): void
    {
        $product = $observer->getData('product');

        if (!$product || in_array($product->getTypeId(), ['bundle', 'grouped'])) {
            return;
        }

        self::$isCreated = ($product->getId() === null);

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            self::$variantsBefore = $product->getTypeInstance()->getUsedProductIds($product);
        }
    }

    /**
     * Getter for isCreated.
     * @return bool
     */
    public static function isProductCreated(): bool
    {
        return self::$isCreated;
    }

    /**
     * Getter for $variantsBefore.
     * @return array
     */
    public static function getVariantsBefore(): array
    {
        return self::$variantsBefore;
    }
}
