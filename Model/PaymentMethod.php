<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Cc;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class PaymentMethod
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class PaymentMethod extends Cc
{
    protected $_code = 'channelengine_payment';

    /**
     * @inheritDoc
     */
    public function canUseForCurrency($currencyCode): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function capture(InfoInterface $payment, $amount): PaymentMethod
    {
        throw new LocalizedException(__('The capture action is not available.'));
    }

    /**
     * @inheritDoc
     */
    public function refund(InfoInterface $payment, $amount): PaymentMethod
    {
        throw new LocalizedException(__('The refund action is not available.'));
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(CartInterface $quote = null): bool
    {
        return false;
    }
}
