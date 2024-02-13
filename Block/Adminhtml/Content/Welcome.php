<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class Welcome
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Welcome extends Template
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
    public function __construct(UrlHelper $urlHelper, Context $context, array $data)
    {
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves start onboarding url.
     *
     * @return string
     */
    public function getStartOnboardingUrl(): string
    {
        return $this->urlHelper->getBackendUrl('channelengine/onboarding/welcome');
    }
}
