<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Welcome;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class ContactChannelEngineSupportMessage
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Welcome
 */
class ContactChannelEngineSupportMessage extends Field
{
    protected $_template = 'partials/contact_ce_support_message.phtml';

    /**
     * @return string
     */
    public function getText()
    {
        if (!empty($this->_request->getParam('store'))) {
            return "";
        }
        return __("Before proceeding with the onboarding process, please contact ChannelEngine support.");
    }
}
