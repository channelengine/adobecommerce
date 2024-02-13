<?php

namespace ChannelEngine\ChannelEngineIntegration\Utility;

use Closure;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;

/**
 * Class CsrfValidatorSkip
 *
 * @package ChannelEngine\ChannelEngineIntegration\Utility
 */
class CsrfValidatorSkip
{
    const WHITELISTED_ACTIONS = [
        'channelengine_asyncprocess_asyncprocess',
        'channelengine_webhooks_webhook'
    ];

    /**
     * @param CsrfValidator $subject
     * @param Closure $proceed
     * @param RequestInterface $request
     * @param ActionInterface $action
     */
    public function aroundValidate(
        CsrfValidator    $subject,
        Closure          $proceed,
        RequestInterface $request,
        ActionInterface $action
    ): void {
        if ($request->getModuleName() === 'channelengine'
            && in_array($request->getFullActionName(), static::WHITELISTED_ACTIONS, true)) {
            return; // Skips CSRF check for ChannelEngine POST routes.
        }

        $proceed($request, $action);
    }
}
