<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

/**
 * Class Account
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Account extends Template
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param UrlHelper $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlHelper $urlHelper,
        array   $data = []
    ) {
        $this->urlHelper = $urlHelper;

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
     * Get current selection name
     *
     * @return string
     */
    public function getCurrentSelectionName(): string
    {
        $storeView = $this->getFirstStoreView();

        return $storeView !== null ? $storeView->getName() : '';
    }

    /**
     * Get current store id.
     *
     * @return int|string
     */
    public function getCurrentStoreId()
    {
        $storeView = $this->getFirstStoreView();

        return $storeView !== null ? $storeView->getId() : '';
    }

    /**
     * Retrieves auth url.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/onboarding/account');
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
     * Retrieves connected store ids.
     *
     * @return array
     *
     * @throws RepositoryNotRegisteredException
     * @throws LocalizedException
     */
    public function getConnectedStoreIds(): array
    {
        return $this->getConfigRepository()->getContexts();
    }

    /**
     * Retrieves first store view.
     *
     * @return Store|null
     *
     * @throws LocalizedException
     * @throws RepositoryNotRegisteredException
     */
    private function getFirstStoreView(): ?Store
    {
        $websites = array_values($this->getWebsites());
        $storeViews = [];

        foreach ($websites as $website) {
            $stores = $website->getGroups();

            foreach ($stores as $store) {
                $storeViews[] = $this->getStores($store);
            }
        }

        $storeViews = array_merge(...$storeViews);
        $connectedStores = $this->getConnectedStoreIds();

        foreach ($storeViews as $key => $storeView) {
            if (in_array($storeView->getId(), $connectedStores, true)) {
                unset($storeViews[$key]);
            }
        }

        if (!$storeViews) {
            return null;
        }

        return !empty($storeViews) ? array_values($storeViews)[0] : null;
    }

    /**
     * @return BaseRepository
     *
     * @throws RepositoryNotRegisteredException
     */
    private function getConfigRepository(): BaseRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return RepositoryRegistry::getRepository(ConfigEntity::getClassName());
    }
}
