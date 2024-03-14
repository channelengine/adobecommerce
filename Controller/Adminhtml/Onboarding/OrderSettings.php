<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding;

use ChannelEngine\ChannelEngineIntegration\DTO\ReturnsSettings;
use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationServiceInterface;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ReturnsSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Traits\GetPostParamsTrait;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class OrderSettings
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding
 */
class OrderSettings extends Action
{
    use SetsContextTrait;
    use GetPostParamsTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var TranslationServiceInterface
     */
    private $translationService;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Saves order settings.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     * @throws QueryFilterInvalidParamException
     */
    public function execute()
    {
        $this->setContext($this->_request);

        $postParams = $this->getPostParams();
        $unknownLinesHandling = $postParams['unknownLinesHandling'] ?? '';
        $importFulfilledOrders = $postParams['importFulfilledOrders'] === '1';
        $fulfilledFromDate = $postParams['fulfilledFromDate'] ?? '';
        $defaultCondition = $postParams['defaultCondition'] ?? '';
        $defaultResolution = $postParams['defaultResolution'] ?? '';
        $merchantFulfilledOrdersSync = $postParams['merchantOrderSync'] === '1';
        $shipmentSync = $postParams['shipmentSync'] === '1';
        $cancellationsSync = $postParams['cancellationSync'] === '1';
        $returnsImport = $postParams['returnsEnabled'] === '1';
        $returnsSync = $postParams['returnsSync'] === '1';

        if (!$this->validateRequest(
            $unknownLinesHandling,
            $importFulfilledOrders,
            $defaultCondition,
            $defaultResolution,
            $returnsImport
        )
        ) {
            return $this->resultJsonFactory->create()->setData(
                [
                    'success' => false,
                    'message' => $this->getTranslationService()->translate('All fields are required.'),
                ]
            );
        }

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
        $returnSettings = new ReturnsSettings($returnsImport, $defaultCondition, $defaultResolution);
        $this->getReturnSettingsService()->setReturnsSettings($returnSettings);

        return $this->resultJsonFactory->create()->setData(['success' => true]);
    }

    /**
     * Validates request.
     *
     * @param $unknownLinesHandling
     * @param $importFulfilledOrders
     * @param $defaultCondition
     * @param $defaultResolution
     * @param $returnsImport
     *
     * @return bool
     */
    private function validateRequest($unknownLinesHandling, $importFulfilledOrders, $defaultCondition, $defaultResolution, $returnsImport): bool
    {
        $returnsValid = true;

        if ($this->productMetadata->getEdition() === 'Enterprise' && $returnsImport) {
            $returnsValid = $defaultCondition !== '' || $defaultResolution !== '';
        }

        return $unknownLinesHandling !== '' && $importFulfilledOrders !== '' &&
            $returnsValid &&
            in_array(
                $unknownLinesHandling,
                [OrdersConfigurationService::EXCLUDE_FULL, OrdersConfigurationService::INCLUDE_FULL],
                true
            );
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }

    /**
     * @return TranslationServiceInterface
     */
    private function getTranslationService(): TranslationServiceInterface
    {
        if ($this->translationService === null) {
            $this->translationService = ServiceRegister::getService(TranslationServiceInterface::class);
        }

        return $this->translationService;
    }

    /**
     * @return ReturnsSettingsService
     */
    private function getReturnSettingsService(): ReturnsSettingsService
    {
        return ServiceRegister::getService(ReturnsSettingsService::class);
    }
}
