<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class Notifications
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Dashboard
 */
class Notifications extends Template
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
     * Retrieves notifications url.
     *
     * @return string
     */
    public function getNotificationUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/notifications');
    }

    /**
     * Retrieves notification details url.
     *
     * @return string
     */
    public function getNotificationDetailsUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/notificationdetails');
    }

    /**
     * Retrieves details url.
     *
     * @return string
     */
    public function getDetailsUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/dashboard/details');
    }
}
