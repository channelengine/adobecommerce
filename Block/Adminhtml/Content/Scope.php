<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

/**
 * Class Scope
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Scope extends Template
{
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Get websites
     *
     * @return Website[]
     */
    public function getWebsites(): array
    {
        $websites = $this->_storeManager->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            $websites = array_intersect_key($websites, array_flip($websiteIds));
        }

        return $websites;
    }

    /**
     * Get store views for specified store group
     *
     * @param Group $group
     *
     * @return Store[]
     */
    public function getStores(Group $group): array
    {
        $stores = $group->getStores();

        if ($storeIds = $this->getStoreIds()) {
            foreach (array_keys($stores) as $storeId) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }

        return $stores;
    }

    /**
     * Retrieves first selected store id.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getFirstSelectedStore(): string
    {
        return ConfigurationManager::getInstance()->getConfigValue('connectedStoreView', '');
    }

    /**
     * Retrieves first selected store name.
     *
     * @param string $id
     *
     * @return string
     *
     * @throws NoSuchEntityException
     */
    public function getSelectedStoreName(string $id): string
    {
        return $this->_storeManager->getStore($id)->getName();
    }
}
