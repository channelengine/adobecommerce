<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use ChannelEngine\ChannelEngineIntegration\Api\StockServiceFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\StockServiceInterface;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products\StockMSIDisabledService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products\StockService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use Magento\Framework\Module\Manager;
use Magento\Framework\App\ObjectManager;

/**
 * Class StockServiceFactory
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class StockServiceFactory implements StockServiceFactoryInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * StockServiceFactory Constructor.
     *
     * @param Manager $moduleManager
     */
    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Creates an instance of StockService based on whether MSI is enabled or disabled.
     *
     * @return StockServiceInterface
     *
     * @throws QueryFilterInvalidParamException
     */
    public function create(): StockServiceInterface
    {
        return $this->isMSIEnabled() ?
            ObjectManager::getInstance()->get(StockService::class) :
            ObjectManager::getInstance()->get(StockMSIDisabledService::class);
    }

    /**
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function isMSIEnabled(): bool
    {
        $stockSettings = $this->getStockService()->getStockSettings();

        return
            $stockSettings &&
            $stockSettings->isEnableMSI() &&
            $this->moduleManager->isEnabled('Magento_Inventory') &&
            $this->moduleManager->isEnabled('Magento_InventoryConfigurationApi') &&
            $this->moduleManager->isEnabled('Magento_InventorySalesAdminUi');
    }

    /**
     * @return StockSettingsService
     */
    private function getStockService(): StockSettingsService
    {
        return ServiceRegister::getService(StockSettingsService::class);
    }
}
