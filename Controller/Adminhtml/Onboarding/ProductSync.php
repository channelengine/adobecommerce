<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappingsTypes;
use ChannelEngine\ChannelEngineIntegration\DTO\ExtraDataAttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\PriceSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\StockSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsSyncConfigService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\SyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsTypesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExportProductsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExtraDataAttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Traits\GetPostParamsTrait;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class PriceSettings
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding
 */
class ProductSync extends Action
{
    use SetsContextTrait;
    use GetPostParamsTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var TranslationService
     */
    private $translationService;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context     $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Saves product sync settings.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws QueryFilterInvalidParamException
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $postParams = $this->getPostParams();

        $postParams['exportProducts'] ? $this->getExportProductsService()->enableProductsExport() :
            $this->getExportProductsService()->disableProductsExport();

        $priceSettingsSaved = $this->savePriceSettings($postParams);

        if (!$priceSettingsSaved['success']) {
            return $this->resultJsonFactory->create()->setData($priceSettingsSaved);
        }

        $stockSettingsSaved = $this->saveStockSettings($postParams);

        if (!$stockSettingsSaved['success']) {
            return $this->resultJsonFactory->create()->setData($stockSettingsSaved);
        }

        $threeLevelSyncSettingsSaved = $this->saveThreeLevelSyncSettings($postParams);

        if (!$threeLevelSyncSettingsSaved['success']) {
            return $this->resultJsonFactory->create()->setData($threeLevelSyncSettingsSaved);
        }

        $syncConfigSaved = $this->saveSyncConfig($postParams);

        if (!$syncConfigSaved['success']) {
            return $this->resultJsonFactory->create()->setData($syncConfigSaved);
        }

        $mappingsSaved = $this->saveAttributeMappings($postParams);

        if (!$mappingsSaved['success']) {
            return $this->resultJsonFactory->create()->setData($mappingsSaved);
        }

        $extraDataMappingSaved = $this->saveExtraDataMappings($postParams['exportProducts'], $postParams['extraDataMappings'] ?? []);

        if (!$extraDataMappingSaved['success']) {
            return $this->resultJsonFactory->create()->setData($extraDataMappingSaved);
        }

        $this->getStateService()->setProductConfigured(true);

        return $this->resultJsonFactory->create()->setData([
            'success' => true
        ]);
    }

    /**
     * @param bool $exportProducts
     * @param array $mappings
     *
     * @return array
     */
    private function saveExtraDataMappings(bool $exportProducts, array $mappings): array
    {
        if (!$exportProducts) {
            return ['success' => true];
        }

        $extraDataMappings = ExtraDataAttributeMappings::fromArray(
            [
                'extraDataAttributeMappings' => $mappings,
            ]
        );

        try {
            $this->getExtraDataMappingService()->setExtraDataAttributeMappings($extraDataMappings);

            return ['success' => true];
        } catch (QueryFilterInvalidParamException $e) {
            return $this->returnError('Failed to save extra data mappings because ' . $e->getMessage());
        }
    }

    /**
     * @param array $postParams
     *
     * @return array|bool[]
     *
     * @throws QueryFilterInvalidParamException
     */
    private function saveAttributeMappings(array $postParams): array
    {
        if (!$postParams['exportProducts']) {
            return ['success' => true];
        }

        $attributeMappings = $postParams['attributeMappings'] ?? [];

        if (empty($attributeMappings['ean'])) {
            return $this->returnError('Ean is required field.');
        }

        $mappings = new AttributeMappings(
            $attributeMappings['productNumber'] ?? '',
            $attributeMappings['name'] ?? '',
            $attributeMappings['description'] ?? '',
            $attributeMappings['category'] ?? '',
            $attributeMappings['shippingCost'] ?? '',
            $attributeMappings['msrp'] ?? '',
            $attributeMappings['purchasePrice'] ?? '',
            $attributeMappings['shippingTime'] ?? '',
            $attributeMappings['brand'] ?? '',
            $attributeMappings['color'] ?? '',
            $attributeMappings['size'] ?? '',
            $attributeMappings['ean']
        );

        $mappingTypes = new AttributeMappingsTypes(
            $attributeMappings['nameType'] ?? '',
            $attributeMappings['descriptionType'] ?? '',
            $attributeMappings['categoryType'] ?? '',
            $attributeMappings['shippingTimeType'] ?? '',
            $attributeMappings['brandType'] ?? '',
            $attributeMappings['colorType'] ?? '',
            $attributeMappings['sizeType'] ?? '',
            $attributeMappings['eanType'] ?? ''
        );

        $this->getMappingService()->setAttributeMappings($mappings);
        $this->getMappingTypesService()->setAttributeMappings($mappingTypes);

        return ['success' => true];
    }

    /**
     * @param array $postParams
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    private function savePriceSettings(array $postParams): array
    {
        if (!$postParams['exportProducts']) {
            return ['success' => true];
        }

        $groupPricing = isset($postParams['groupPricing']) ? (int)$postParams['groupPricing'] : '';
        $priceAttribute = $postParams['priceAttribute'] ?? '';
        $customerGroup = $postParams['customerGroup'] ?? '';
        $quantity = $postParams['quantity'] ?? '';

        if ($groupPricing === '' || ($groupPricing === 0 && empty($priceAttribute))
            || ($groupPricing !== 0 && ($customerGroup === '' || empty($quantity)))) {
            return $this->returnError('Price settings are not correctly set.');
        }

        if ($groupPricing !== 0 &&
            (!filter_var($quantity, FILTER_VALIDATE_INT) || (int)$quantity < 0)) {
            return $this->returnError('Attribute quantity is required field.');
        }

        $priceSettings = new PriceSettings(
            $groupPricing,
            $priceAttribute,
            $customerGroup !== '' ? $customerGroup : 0,
            $quantity !== '' ? (int)$quantity : 0
        );
        $this->getPriceSettingsService()->setPriceSettings($priceSettings);

        return ['success' => true];
    }

    /**
     * @param array $postParams
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    private function saveStockSettings(array $postParams): array
    {
        if (!$postParams['exportProducts']) {
            return ['success' => true];
        }

        $enableStockSync = $postParams['enableStockSync'] === '1' ?? false;
        $inventories = $postParams['selectedInventories'] ?? [];
        $quantity = $postParams['stockQuantity'] ?? '';

        if ($enableStockSync && $inventories === []) {
            return $this->returnError('Please select at least one inventory.');
        }

        if ($enableStockSync && (!is_numeric($quantity) || (int)$quantity < 0)) {
            return $this->returnError('Stock quantity is required.');
        }

        $settings = new StockSettings($enableStockSync, $inventories, $quantity);
        $this->getStockSettingsService()->setStockSettings($settings);

        return ['success' => true];
    }

    /**
     * @param array $postParams
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    private function saveThreeLevelSyncSettings(array $postParams): array
    {
        if (!$postParams['exportProducts']) {
            return ['success' => true];
        }

        $threeLevelSync = $postParams['threeLevelSync'] ?? [];
        $enableThreeLevelSync = $threeLevelSync['enableThreeLevelSync'] === '1' ?? false;
        $newSyncAttribute = $threeLevelSync['syncAttribute'];

        $currentThreeLevelSyncSettings = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
        $oldSyncAttribute = '';
        if ($currentThreeLevelSyncSettings) {
            $oldSyncAttribute = $currentThreeLevelSyncSettings->getSyncAttribute();
        }

        $attributeToBeSaved = $enableThreeLevelSync || !$currentThreeLevelSyncSettings ? $newSyncAttribute : $oldSyncAttribute;

        $settings = new ThreeLevelSyncSettings($enableThreeLevelSync, $attributeToBeSaved, false);

        $this->getThreeLevelSyncSettingsService()->setThreeLevelSyncSettings($settings);

        return ['success' => true];
    }

    /**
     * @param array $postParams
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    private function saveSyncConfig(array $postParams): array
    {
        if (!$postParams['exportProducts']) {
            return ['success' => true];
        }

        $threeLevelSync = $postParams['threeLevelSync'] ?? [];
        $enableThreeLevelSync = $threeLevelSync['enableThreeLevelSync'] === '1' ?? false;
        $newSyncAttribute = $threeLevelSync['syncAttribute'];
        $enableStockSync = $postParams['enableStockSync'] === '1' ?? false;

        $currentThreeLevelSyncSettings = $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
        $oldSyncAttribute = '';
        if ($currentThreeLevelSyncSettings) {
            $oldSyncAttribute = $currentThreeLevelSyncSettings->getSyncAttribute();
        }

        $attributeToBeSaved = $enableThreeLevelSync || !$currentThreeLevelSyncSettings ? $newSyncAttribute : $oldSyncAttribute;

        $settings = new SyncConfig();
        $settings->setEnabledStockSync($enableStockSync);
        $settings->setThreeLevelSyncAttribute($attributeToBeSaved);
        $settings->setThreeLevelSyncStatus($enableThreeLevelSync);
        $this->getProductsSyncConfigService()->set($settings);

        return ['success' => true];
    }

    /**
     * @param $message
     *
     * @return array
     */
    private function returnError($message): array
    {
        return [
            'success' => false,
            'message' => $this->getTranslationService()->translate($message),
        ];
    }

    /**
     * @return PriceSettingsService
     */
    private function getPriceSettingsService(): PriceSettingsService
    {
        return ServiceRegister::getService(PriceSettingsService::class);
    }

    /**
     * Retrieves instance of ProductsSyncConfigService.
     *
     * @return ProductsSyncConfigService
     */
    protected function getProductsSyncConfigService()
    {
        return ServiceRegister::getService(ProductsSyncConfigService::class);
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
     * @return AttributeMappingsService
     */
    private function getMappingService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }

    /**
     * @return AttributeMappingsTypesService
     */
    private function getMappingTypesService(): AttributeMappingsTypesService
    {
        return ServiceRegister::getService(AttributeMappingsTypesService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * @return ExtraDataAttributeMappingsService
     */
    private function getExtraDataMappingService(): ExtraDataAttributeMappingsService
    {
        return ServiceRegister::getService(ExtraDataAttributeMappingsService::class);
    }

    /**
     * @return TranslationService
     */
    private function getTranslationService(): TranslationService
    {
        if ($this->translationService === null) {
            $this->translationService = ServiceRegister::getService(TranslationService::class);
        }

        return $this->translationService;
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
