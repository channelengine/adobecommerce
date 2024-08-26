<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\DTO\OrderStatusMappings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\OrderStatusMappingService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;

/**
 * Class OrderSettings
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class OrderSettings extends Template
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var StatusCollectionFactory
     */
    private $statusCollectionFactory;

    /**
     * @param UrlHelper $urlHelper
     * @param ProductMetadataInterface $productMetadata
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        UrlHelper                $urlHelper,
        ProductMetadataInterface $productMetadata,
        StatusCollectionFactory  $statusCollectionFactory,
        Context                  $context,
        array                    $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->productMetadata = $productMetadata;
        $this->statusCollectionFactory = $statusCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnterprise(): bool
    {
        return $this->productMetadata->getEdition() === 'Enterprise';
    }

    /**
     * Retrieves order settings.
     *
     * @return OrderSyncConfig|null
     */
    public function getOrderSettings(): ?OrderSyncConfig
    {
        return $this->getOrderSettingsService()->getOrderSyncConfig();
    }

    /**
     * Retrieves order settings url.
     *
     * @return string
     */
    public function getOrderSettingsUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/onboarding/ordersettings');
    }

    /**
     * Retrieves state url.
     *
     * @return string
     */
    public function getStateUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/content/state');
    }

    /**
     * Retrieves product sync page url.
     *
     * @return string
     */
    public function getProductSyncPageUrl(): string
    {
        return $this->urlHelper->getBackendUrl(
            'channelengine/content/productsync',
            [
                'page' => StateService::PRODUCT_CONFIGURATION,
                'storeId' => ConfigurationManager::getInstance()->getContext(),
            ]
        );
    }

    /**
     * Retrieves current store id.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getStoreId(): string
    {
        return $this->getStoreService()->getStoreId();
    }

    /**
     * Checks if Marketplace order sync from date field should be rendered.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function shouldRenderOrdersFromDateField(): bool
    {
        $state = $this->getStateService()->getCurrentState();

        return $state === StateService::ORDER_SETTINGS;
    }

    /**
     * Returns Magento order status options
     *
     * @return array
     */
    public function getOrderStatusOptions(): array
    {
        return $this->statusCollectionFactory->create()->toOptionArray();
    }

    /**
     * Retrieves order status mappings.
     *
     * @return OrderStatusMappings|null
     */
    public function getOrderStatusMappings(): ?OrderStatusMappings
    {
        try {
            return $this->getOrderStatusMappingService()->getOrderStatusMappings();
        } catch (QueryFilterInvalidParamException $e) {
            return null;
        }
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }

    /**
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * @return OrderStatusMappingService
     */
    private function getOrderStatusMappingService(): OrderStatusMappingService
    {
        return ServiceRegister::getService(OrderStatusMappingService::class);
    }
}
