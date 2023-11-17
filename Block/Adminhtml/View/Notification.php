<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Registry;
use Magento\Shipping\Block\Adminhtml\Order\Tracking\View;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\Config;

/**
 * Class Notification
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\View
 */
class Notification extends View
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param Config $shippingConfig
     * @param Registry $registry
     * @param CarrierFactory $carrierFactory
     * @param Session $session
     * @param array $data
     * @param ShippingHelper|null $shippingHelper
     */
    public function __construct(
        Context         $context,
        Config          $shippingConfig,
        Registry        $registry,
        CarrierFactory  $carrierFactory,
        Session         $session,
        array           $data = [],
        ?ShippingHelper $shippingHelper = null
    ) {
        $this->session = $session;
        parent::__construct($context, $shippingConfig, $registry, $carrierFactory, $data, $shippingHelper);
    }

    /**
     * Retrieves ChannelEngine notification from session.
     *
     * @return string
     */
    public function getNotification(): string
    {
        $notification = $this->session->getData('channel_engine_notification', true);

        return $notification ?: '';
    }
}
