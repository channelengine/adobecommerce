<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\Api\SourceCollectionFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\PriceSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\StockSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExportProductsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\View\Element\Template;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeHandler;
use Magento\Framework\Module\Manager;

/**
 * Class ProductSync
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class ProductSync extends Template
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;
    /**
     * @var Collection
     */
    private $customerGroup;
    /**
     * @var CollectionFactory
     */
    private $productAttributes;
    /**
     * @var ConfigurableAttributeHandler
     */
    private $configurableAttributeHandler;
    /**
     * @var Manager
     */
    private $moduleManager;
    /**
     * @var SourceCollectionFactoryInterface
     */
    private $sourceCollectionFactory;

    /**
     * @param UrlHelper $urlHelper
     * @param Collection $customerGroup
     * @param CollectionFactory $productAttributes
     * @param Context $context
     * @param Manager $moduleManager
     * @param ConfigurableAttributeHandler $configurableAttributeHandler
     * @param SourceCollectionFactoryInterface $sourceCollectionFactory
     * @param array $data
     */
    public function __construct(
        UrlHelper         $urlHelper,
        Collection        $customerGroup,
        CollectionFactory $productAttributes,
        Context           $context,
        Manager           $moduleManager,
        ConfigurableAttributeHandler $configurableAttributeHandler,
        SourceCollectionFactoryInterface $sourceCollectionFactory,
        array             $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->customerGroup = $customerGroup;
        $this->productAttributes = $productAttributes;
        $this->configurableAttributeHandler = $configurableAttributeHandler;
        $this->moduleManager = $moduleManager;
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves customer groups.
     *
     * @return array
     */
    public function getCustomerGroups(): array
    {
        return $this->customerGroup->toOptionArray();
    }

    /**
     * Retrieves inventory sources.
     *
     * @return array
     */
    public function getInventorySources(): array
    {
        return $this->sourceCollectionFactory->create() ?
            $this->sourceCollectionFactory->create()->toOptionArray() : [];
    }

    /**
     * Retrieves Magento product attributes.
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAttributes(): array
    {
        $attributes[] = $this->getNotMappedOption();
        $storeId = $this->getStoreService()->getStoreId();
        $collection = $this->productAttributes->create()
            ->addFieldToFilter(
                'main_table.frontend_input',
                ['in' => ['select', 'text', 'textarea', 'date', 'datetime', 'boolean', 'multiselect', 'weight']]
            )->addStoreLabel($storeId);

        foreach ($collection as $item) {
            if ($item->getStoreLabel()) {
                $attributes[] = [
                    'value' => $item->getAttributeCode(),
                    'label' => $item->getStoreLabel(),
                    'type' => $item->getFrontendInput(),
                ];
            }
        }

        return $attributes;
    }

    /**
     * Retrieves Magento product attributes which are configurable.
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getConfigurableAttributes(): array
    {
        $attributes = [];
        $storeId = $this->getStoreService()->getStoreId();
        $applicableAttributes = $this->configurableAttributeHandler->getApplicableAttributes();

        foreach ($applicableAttributes as $attribute) {
            if ($attribute->getAttributeCode() !== 'manufacturer') {
                $attributes[] = [
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getStoreLabel(),
                    'type' => $attribute->getFrontendInput(),
                ];
            }
        }

        return $attributes;
    }

    /**
     * Retrieves "Not mapped" option.
     *
     * @return array
     */
    public function getNotMappedOption(): array
    {
        return [
            'value' => 'not_mapped',
            'label' => __('Not mapped'),
            'type' => ''
        ];
    }

    /**
     * Retrieve default selected attribute.
     *
     * @param array $attributes
     * @param string $name
     *
     * @return string[]
     */
    public function getDefaultSelectedAttribute(array $attributes, string $name): array
    {
        foreach ($attributes as $attribute) {
            if ($attribute['label'] === $name) {
                return $attribute;
            }
        }

        return  [
            'value' => '',
            'label' => '',
        ];
    }

    /**
     * Retrieve first attribute if possible.
     *
     * @param array $attributes
     *
     * @return string[]
     */
    public function getFirstAttribute(array $attributes): array
    {
        if ($attributes) {
            return $attributes[0];
        }

        return  [
            'value' => '',
            'label' => '',
        ];
    }

    /**
     * Retrieves price attributes.
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getPriceAttributes(): array
    {
        $attributes = [];
        $storeId = $this->getStoreService()->getStoreId();
        $collection = $this->productAttributes->create()
            ->addFieldToFilter(
                'main_table.frontend_input',
                ['in' => ['price']]
            )->addStoreLabel($storeId);

        $attributes[] = $this->getNotMappedOption();

        foreach ($collection as $item) {
            $attributes[] = [
                'value' => $item->getAttributeCode(),
                'label' => $item->getStoreLabel(),
            ];
        }

        return $attributes;
    }

    /**
     * Checks if Merchant product number mapping should be rendered.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function shouldRenderProductNumberMapping(): bool
    {
        $state = $this->getStateService()->getCurrentState();

        return $state === StateService::PRODUCT_CONFIGURATION;
    }

    /**
     * Retrieves product settings save url.
     *
     * @return string
     */
    public function getProductSaveUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/onboarding/productsync');
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
     * Retrieves extra data attributes url.
     *
     * @return string
     */
    public function getExtraDataGetAttributesUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/extradata/getattributes');
    }

    /**
     * Retrieves extra data mappings usl.
     *
     * @return string
     */
    public function getExtraDataMappingsUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/extradata/getmappings');
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
     * Retrieves attributes mappings.
     *
     * @return AttributeMappings|null
     */
    public function getAttributeMappings(): ?AttributeMappings
    {
        try {
            return $this->getAttributeMappingsService()->getAttributeMappings();
        } catch (QueryFilterInvalidParamException $e) {
            return null;
        }
    }

    /**
     * Retrieves price settings.
     *
     * @return PriceSettings|null
     */
    public function getPriceSettings(): ?PriceSettings
    {
        try {
            return $this->getPriceSettingsService()->getPriceSettings();
        } catch (QueryFilterInvalidParamException $e) {
            return null;
        }
    }

    /**
     * Retrieves stock settings.
     *
     * @return StockSettings|null
     */
    public function getStockSettings(): ?StockSettings
    {
        try {
            return $this->getStockSettingsService()->getStockSettings();
        } catch (QueryFilterInvalidParamException $e) {
            return null;
        }
    }

    /**
     * Retrieves three level sync settings.
     *
     * @return ThreeLevelSyncSettings|null
     */
    public function getThreeLevelSyncSettings(): ?ThreeLevelSyncSettings
    {
        try {
            return $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
        } catch (QueryFilterInvalidParamException $e) {
            return null;
        }
    }

    /**
     * Checks if export products is enabled.
     *
     * @return bool
     */
    public function isExportProductsEnabled(): bool
    {
        return $this->getExportProductsService() && $this->getExportProductsService()->isExportProductsEnabled();
    }

    /**
     * Checks if MSI is enabled in shop.
     *
     * @return bool
     */
    public function isMSIEnabledInShop(): bool
    {
        return
            $this->moduleManager->isEnabled('Magento_Inventory') &&
            $this->moduleManager->isEnabled('Magento_InventoryConfigurationApi') &&
            $this->moduleManager->isEnabled('Magento_InventorySalesAdminUi');
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }

    /**
     * @return AttributeMappingsService
     */
    private function getAttributeMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }

    /**
     * @return PriceSettingsService
     */
    private function getPriceSettingsService(): PriceSettingsService
    {
        return ServiceRegister::getService(PriceSettingsService::class);
    }

    /**
     * @return StockSettingsService
     */
    private function getStockSettingsService(): StockSettingsService
    {
        return ServiceRegister::getService(StockSettingsService::class);
    }

    /**
     * @return ThreeLevelSyncSettingsService
     */
    private function getThreeLevelSyncSettingsService(): ThreeLevelSyncSettingsService
    {
        return ServiceRegister::getService(ThreeLevelSyncSettingsService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * Retrieves ExportProducts service.
     *
     * @return ExportProductsService
     */
    private function getExportProductsService(): ExportProductsService
    {
        return ServiceRegister::getService(ExportProductsService::class);
    }
}
