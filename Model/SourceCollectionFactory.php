<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use ChannelEngine\ChannelEngineIntegration\Api\SourceCollectionFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Magento\Inventory\Model\ResourceModel\Source\Collection as SourceCollection;
use Magento\Framework\Module\Manager;
use Magento\Framework\App\ObjectManager;

/**
 * Class SourceCollectionFactory
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class SourceCollectionFactory implements SourceCollectionFactoryInterface
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
     * Creates an instance of SourceCollection based on whether MSI is enabled or disabled.
     *
     * @return SourceCollection|null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function create(): ?SourceCollection
    {
        return $this->isMSIEnabled() ?
            ObjectManager::getInstance()->get(SourceCollection::class) : null;
    }
    /**
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function isMSIEnabled(): bool
    {
        return
            $this->moduleManager->isEnabled('Magento_Inventory') &&
            $this->moduleManager->isEnabled('Magento_InventoryConfigurationApi') &&
            $this->moduleManager->isEnabled('Magento_InventorySalesAdminUi');
    }
}
