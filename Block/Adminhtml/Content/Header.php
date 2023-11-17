<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Exception;
use Magento\Framework\View\Element\Template;

/**
 * Class Header
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class Header extends Template
{
    /**
     * Retrieves account name.
     *
     * @return string
     */
    public function getAccountName(): string
    {
        $authService = $this->getAuthService();

        try {
            return $authService->getAuthInfo()->getAccountName();
        } catch (Exception $exception) {
            return '';
        }
    }

    /**
     * Retrieves authorization service.
     *
     * @return AuthorizationService
     */
    private function getAuthService(): AuthorizationService
    {
        return ServiceRegister::getService(AuthorizationService::class);
    }
}
