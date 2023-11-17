<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class Transactions
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Transactions extends Template
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param UrlHelper $urlHelper
     * @param Context $context
     * @param array $data
     */
    public function __construct(UrlHelper $urlHelper, Context $context, array $data = [])
    {
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves subpage url.
     *
     * @param string $page
     *
     * @return string
     */
    public function getSubpageUrl(string $page): string
    {
        return $this->urlHelper->getBackendUrl(
            'channelengine/content/state',
            [
                'page' => $page,
                'storeId' => ConfigurationManager::getInstance()->getContext()
            ]
        );
    }

    /**
     * Retrieves transaction logs url.
     *
     * @return string
     */
    public function getLogsUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/transactions/transactionlogs');
    }

    /**
     * Retrieves details url.
     *
     * @return string
     */
    public function getDetailsUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/transactions/details');
    }

    /**
     * Retrieves state url.
     *
     * @return string
     */
    public function getStateUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/content/state', ['page' => 'transactions']);
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
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }
}
