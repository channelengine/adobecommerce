<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappingsTypes;
use ChannelEngine\ChannelEngineIntegration\DTO\ExtraDataAttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\PriceSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\ReturnsSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\StockSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\DTO\AuthInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Exceptions\CurrencyMismatchException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\SyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsSyncConfigService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpClient;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsTypesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExportProductsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExtraDataAttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ReturnsSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Traits\GetPostParamsTrait;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Save
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration
 */
class Save extends Action
{
    use SetsContextTrait;
    use GetPostParamsTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var AuthorizationService
     */
    private $authService;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context               $context,
        JsonFactory           $resultJsonFactory,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Saves configuration.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $params = $this->getPostParams();

        try {
            if (isset($params['apiKey'], $params['accountName'], $params['storeId'])) {
                $this->saveAccountData($params['apiKey'], $params['accountName'], $params['storeId']);
            }

            $params['exportProducts'] ? $this->getExportProductsService()->enableProductsExport() :
                $this->getExportProductsService()->disableProductsExport();

            $this->savePriceSettings(
                $params['exportProducts'],
                isset($params['groupPricing']) ? (int)$params['groupPricing'] : '',
                $params['priceAttribute'] ?? '',
                $params['customerGroup'] ?? '',
                $params['quantity'] ? (int)$params['quantity'] : 0
            );

            $this->saveStockSettings(
                $params['exportProducts'],
                $params['enableStockSync'] === '1' ?? false,
                $params['selectedInventories'] ?? [],
                $params['stockQuantity'] ?? '',
                $params['enableMSI'] === '1' ?? false
            );

            $this->saveThreeLevelSyncSettings($params['exportProducts'], $params['threeLevelSync'] ?? []);

            $this->saveSyncConfig($params['enableStockSync'] === '1' ?? false, $params['threeLevelSync'] ?? []);

            $this->saveAttributeMappings($params['exportProducts'], $params['attributeMappings'] ?? []);

            $this->saveExtraDataMappings($params['exportProducts'], $params['extraDataMappings'] ?? []);

            $this->saveOrderSyncSettings(
                $params['unknownLinesHandling'] ?? '',
                $params['importFulfilledOrders'] ?? '',
                $params['merchantOrderSync'] === '1',
                $params['shipmentSync'] === '1',
                $params['cancellationSync'] === '1',
                $params['fulfilledFromDate'] ?? '',
                isset($params['returnsSync']) && $params['returnsSync'] === '1'
            );

            $this->saveReturnSettings(
                $params['returnsEnabled'] === '1',
                $params['defaultCondition'] ?? '',
                $params['defaultResolution'] ?? ''
            );
        } catch (BaseException $e) {
            return $this->resultJsonFactory->create()->setData(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return $this->resultJsonFactory->create()->setData(
            [
                'success' => true,
                'message' => __('Configuration saved successfully.'),
            ]
        );
    }

    /**
     * @param string $apiKey
     * @param string $accountName
     * @param string $storeId
     *
     * @return void
     *
     * @throws BaseException
     */
    private function saveAccountData(string $apiKey, string $accountName, string $storeId): void
    {
        try {
            $currency = $this->storeManager->getStore($storeId)->getCurrentCurrencyCode();
            $this->getAuthService()->validateAccountInfo($apiKey, $accountName, $currency);
            $orderProxy = new Proxy(ServiceRegister::getService(HttpClient::class), $accountName, $apiKey);
            $orderProxy->getNew();
            $authInfo = AuthInfo::fromArray(['account_name' => $accountName, 'api_key' => $apiKey]);
            $this->getAuthService()->setAuthInfo($authInfo);
            $this->getStateService()->setAccountConfigured(true);
            $this->getStoreService()->setStoreId($storeId);
        } catch (QueryFilterInvalidParamException|HttpCommunicationException|
        RequestNotSuccessfulException|HttpRequestException|NoSuchEntityException $e) {
            throw new BaseException(__('Invalid API key or Account name.'));
        } catch (CurrencyMismatchException $e) {
            throw new BaseException(__('Currency mismatch detected. Please make sure that store currency matches ChannelEngine.'));
        }
    }

    /**
     * @param bool $exportProducts
     * @param int $groupPricing
     * @param string $priceAttribute
     * @param string $customerGroup
     * @param int $quantity
     * @return void
     * @throws BaseException
     * @throws QueryFilterInvalidParamException
     */
    private function savePriceSettings(
        bool $exportProducts,
        int    $groupPricing,
        string $priceAttribute,
        string $customerGroup = '',
        int    $quantity = 0
    ): void {
        if (!$exportProducts) {
            return;
        }

        if (($groupPricing === 0 && empty($priceAttribute))
            || ($groupPricing !== 0 && ($customerGroup === '' || empty($quantity)))) {
            throw new BaseException(__('Price settings are not correctly set.'));
        }

        if ($groupPricing !== 0 &&
            (!filter_var($quantity, FILTER_VALIDATE_INT) || (int)$quantity < 0)) {
            throw new BaseException(__('Attribute quantity is required field.'));
        }

        $priceSettings = new PriceSettings(
            $groupPricing,
            $priceAttribute,
            $customerGroup !== '' ? $customerGroup : 0,
            (int)$quantity
        );
        $this->getPriceSettingsService()->setPriceSettings($priceSettings);
    }

    /**
     * @param bool $exportProducts
     * @param bool $enableStockSync
     * @param array $inventories
     * @param int $quantity
     * @param bool $enableMSI
     * @return void
     *
     * @throws BaseException
     * @throws QueryFilterInvalidParamException
     */
    private function saveStockSettings(
        bool $exportProducts,
        bool $enableStockSync,
        array $inventories,
        int $quantity,
        bool $enableMSI
    ): void
    {
        if (!$exportProducts) {
            return;
        }

        if ($enableStockSync && $inventories === [] && $enableMSI) {
            throw new BaseException(__('Please select at least one inventory.'));
        }

        if ($enableStockSync && (!is_numeric($quantity) || (int)$quantity < 0)) {
            throw new BaseException(__('Stock quantity is required.'));
        }

        $settings = new StockSettings($enableStockSync, $inventories, $quantity, $enableMSI);
        $this->getStockSettingsService()->setStockSettings($settings);
    }

    /**
     * @param bool $exportProducts
     * @param array $threeLevelSync
     *
     * @return void
     *
     * @throws BaseException
     * @throws QueryFilterInvalidParamException
     */
    private function saveThreeLevelSyncSettings(bool $exportProducts, array $threeLevelSync):void
    {
        if (!$exportProducts) {
            return;
        }

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
    }

    /**
     * @param bool $exportProducts
     * @param array $threeLevelSync
     *
     * @return void
     *
     * @throws BaseException
     * @throws QueryFilterInvalidParamException
     */
    private function saveSyncConfig(bool $enableStockSync, array $threeLevelSync): void
    {
        $enableThreeLevelSync = $threeLevelSync['enableThreeLevelSync'] === '1' ?? false;
        $newSyncAttribute = $threeLevelSync['syncAttribute'];

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
    }

    /**
     * @param bool $exportProducts
     * @param array $attributeMappings
     *
     * @return void
     *
     * @throws BaseException
     * @throws QueryFilterInvalidParamException
     */
    private function saveAttributeMappings(bool $exportProducts, array $attributeMappings): void
    {
        if (!$exportProducts) {
            return;
        }

        if (empty($attributeMappings['ean'])) {
            throw new BaseException(__('Ean is required field.'));
        }

        $oldMappings = $this->getMappingService()->getAttributeMappings();

        $mappings = new AttributeMappings(
            $oldMappings ? $oldMappings->getMerchantProductNumber() : '',
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
    }

    /**
     * @param string $unknownLinesHandling
     * @param bool $importFulfilledOrders
     * @param bool $merchantFulfilledOrdersSync
     * @param bool $shipmentSync
     * @param bool $cancellationsSync
     * @param string $fulfilledFromDate
     * @param bool $returnsSync
     *
     * @return void
     *
     * @throws BaseException
     */
    private function saveOrderSyncSettings(
        string $unknownLinesHandling,
        bool $importFulfilledOrders,
        bool $merchantFulfilledOrdersSync,
        bool $shipmentSync,
        bool $cancellationsSync,
        string $fulfilledFromDate,
        bool $returnsSync
    ): void {
        if (!$this->validateOrderSyncSettings(
            $unknownLinesHandling,
            $importFulfilledOrders,
            $merchantFulfilledOrdersSync,
            $shipmentSync,
            $cancellationsSync
        )) {
            throw new BaseException(__('Order sync settings are incorrect.'));
        }

        try {
            $orderSettings = new OrderSyncConfig();
            $orderSettings->setUnknownLinesHandling($unknownLinesHandling);
            $orderSettings->setEnableOrdersByMarketplaceSync($importFulfilledOrders);
            $orderSettings->setEnableOrdersByMerchantSync($merchantFulfilledOrdersSync);
            $orderSettings->setEnableShipmentInfoSync($shipmentSync);
            $orderSettings->setEnableOrderCancellationSync($cancellationsSync);
            $orderSettings->setFromDate($fulfilledFromDate);
            $orderSettings->setEnableReturnsSync($returnsSync);

            $this->getOrderSettingsService()->saveOrderSyncConfig($orderSettings);
            $this->getStateService()->setOrderConfigured(true);
        } catch (QueryFilterInvalidParamException $e) {
            throw new BaseException(__('Failed to save order sync settings'));
        }
    }

    /**
     * @param $returnsEnabled
     * @param $defaultCondition
     * @param $defaultResolution
     *
     * @return void
     *
     * @throws BaseException
     * @throws QueryFilterInvalidParamException
     */
    private function saveReturnSettings($returnsEnabled, $defaultCondition, $defaultResolution): void
    {
        if ($this->productMetadata->getEdition() !== 'Enterprise') {
            return;
        }

        if ($returnsEnabled && (empty($defaultCondition) || empty($defaultResolution))) {
            throw new BaseException(__('Return settings are incorrect.'));
        }

        $returnSettings = new ReturnsSettings($returnsEnabled, $defaultCondition, $defaultResolution);
        $this->getReturnSettingsService()->setReturnsSettings($returnSettings);
    }

    /**
     * @param array $mappings
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    private function saveExtraDataMappings(bool $exportProducts, array $mappings): void
    {
        if (!$exportProducts) {
            return;
        }

        $extraDataMappings = ExtraDataAttributeMappings::fromArray(
            [
                'extraDataAttributeMappings' => $mappings,
            ]
        );

        $this->getExtraDataMappingService()->setExtraDataAttributeMappings($extraDataMappings);
    }

    /**
     * @param $unknownLinesHandling
     * @param $importFulfilledOrders
     * @param $merchantFulfilledOrders
     * @param $shipmentsSync
     * @param $cancellationsSync
     *
     * @return bool
     */
    private function validateOrderSyncSettings(
        $unknownLinesHandling,
        $importFulfilledOrders,
        $merchantFulfilledOrders,
        $shipmentsSync,
        $cancellationsSync
    ): bool {
        return $unknownLinesHandling !== '' || $importFulfilledOrders !== '' ||
            $merchantFulfilledOrders !== '' || $shipmentsSync !== '' || $cancellationsSync !== '' ||
            in_array(
                $unknownLinesHandling,
                [OrdersConfigurationService::EXCLUDE_FULL, OrdersConfigurationService::INCLUDE_FULL],
                true
            );
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
     * @return AuthorizationService
     */
    private function getAuthService(): AuthorizationService
    {
        if ($this->authService === null) {
            $this->authService = ServiceRegister::getService(AuthorizationService::class);
        }

        return $this->authService;
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
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
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }

    /**
     * @return ReturnsSettingsService
     */
    private function getReturnSettingsService(): ReturnsSettingsService
    {
        return ServiceRegister::getService(ReturnsSettingsService::class);
    }

    /**
     * @return ExtraDataAttributeMappingsService
     */
    private function getExtraDataMappingService(): ExtraDataAttributeMappingsService
    {
        return ServiceRegister::getService(ExtraDataAttributeMappingsService::class);
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
