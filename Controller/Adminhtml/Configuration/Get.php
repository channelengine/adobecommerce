<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration;

use ChannelEngine\ChannelEngineIntegration\DTO\AttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\ExtraDataAttributeMappings;
use ChannelEngine\ChannelEngineIntegration\DTO\PriceSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\StockSettings;
use ChannelEngine\ChannelEngineIntegration\DTO\ThreeLevelSyncSettings;
use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\DTO\AuthInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Exceptions\FailedToRetrieveAuthInfoException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExportProductsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExtraDataAttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Get
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Configuration
 */
class Get extends Action
{
    use SetsContextTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

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
     * Retrieves configuration data.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);

        $extraData = $this->getExtraDataMappings();

        try {
            return $this->resultJsonFactory->create()->setData(
                [
                    'success' => true,
                    'accountData' => $this->getAccountData()->toArray(),
                    'priceData' => $this->getPriceData() ? $this->getPriceData()->toArray() : [],
                    'stockData' => $this->getStockData() ? $this->getStockData()->toArray() : [],
                    'threeLevelSyncData' => $this->getThreeLevelSyncData() ? $this->getThreeLevelSyncData()->toArray() : [],
                    'attributesData' => $this->getAttributeMappingsData() ? $this->getAttributeMappingsData()->toArray() : [],
                    'ordersData' => $this->getOrderSyncSettings()->toArray(),
                    'extraData' => $extraData ? $extraData->getMappings() : [],
                    'exportProducts' => $this->getExportProductsService()->isExportProductsEnabled()
                ]
            );
        } catch (FailedToRetrieveAuthInfoException|QueryFilterInvalidParamException $e) {
            return $this->resultJsonFactory->create()->setData(
                [
                    'success' => false,
                    'message' => __('Failed to retrieve configuration data.'),
                ]
            );
        }
    }

    /**
     * @return AuthInfo
     *
     * @throws FailedToRetrieveAuthInfoException
     * @throws QueryFilterInvalidParamException
     */
    private function getAccountData(): AuthInfo
    {
        return $this->getAuthService()->getAuthInfo();
    }

    /**
     * @return PriceSettings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getPriceData(): ?PriceSettings
    {
        return $this->getPriceService()->getPriceSettings();
    }

    /**
     * @return StockSettings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getStockData(): ?StockSettings
    {
        return $this->getStockService()->getStockSettings();
    }

    /**
     * @return ThreeLevelSyncSettings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getThreeLevelSyncData(): ?ThreeLevelSyncSettings
    {
        return $this->getThreeLevelSyncSettingsService()->getThreeLevelSyncSettings();
    }

    /**
     * @return AttributeMappings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getAttributeMappingsData(): ?AttributeMappings
    {
        return $this->getAttributeMappingsService()->getAttributeMappings();
    }

    /**
     * @return OrderSyncConfig
     */
    private function getOrderSyncSettings(): OrderSyncConfig
    {
        return $this->getOrderSettingsService()->getOrderSyncConfig();
    }

    /**
     * @return ExtraDataAttributeMappings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getExtraDataMappings(): ?ExtraDataAttributeMappings
    {
        return $this->getExtraDataMappingService()->getExtraDataAttributeMappings();
    }

    /**
     * @return AuthorizationService
     */
    private function getAuthService(): AuthorizationService
    {
        return ServiceRegister::getService(AuthorizationService::class);
    }

    /**
     * @return PriceSettingsService
     */
    private function getPriceService(): PriceSettingsService
    {
        return ServiceRegister::getService(PriceSettingsService::class);
    }

    /**
     * @return StockSettingsService
     */
    private function getStockService(): StockSettingsService
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
    private function getAttributeMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }

    /**
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
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
