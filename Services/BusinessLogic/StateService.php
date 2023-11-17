<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class StateService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class StateService
{
    public const WELCOME_STATE = 'onboarding';
    public const ACCOUNT_CONFIGURATION = 'account_configuration';
    public const PRODUCT_CONFIGURATION = 'product_configuration';
    public const ORDER_SETTINGS = 'order_settings';
    public const ENABLE_AND_SYNC = 'enable_and_sync';
    public const DASHBOARD = 'dashboard';
    public const CONFIG = 'config';
    public const TRANSACTIONS = 'transactions';

    /**
     * Retrieves current plugin state.
     *
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getCurrentState(): string
    {
        $page = self::DASHBOARD;

        if (!$this->isAccountConfigured()) {
            $page = self::WELCOME_STATE;
        }

        if (!$this->isAccountConfigured() && $this->getOnboardingStarted()) {
            $page = self::ACCOUNT_CONFIGURATION;
        }

        if ($this->isAccountConfigured() && !$this->isProductConfigured()) {
            $page = self::PRODUCT_CONFIGURATION;
        }

        if ($this->isProductConfigured() && !$this->isOrderConfigured()) {
            $page = self::ORDER_SETTINGS;
        }

        if ($this->isOrderConfigured() && !$this->isInitialSyncInProgress() && !$this->isOnboardingCompleted()
            && !$this->isOnboardingCompletedIntegrationDisabled()) {
            $page = self::ENABLE_AND_SYNC;
        }

        return $page;
    }

    /**
     * Sets manualProductSyncInProgress value.
     *
     * @param bool $value
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setManualProductSyncInProgress(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('manualProductSyncInProgress', $value);
    }

    /**
     * Retrieves manualProductSyncInProgress value.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isManualProductSyncInProgress(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('manualProductSyncInProgress', false);
    }

    /**
     * Sets manualOrderSyncInProgress value.
     *
     * @param bool $value
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setManualOrderSyncInProgress(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('manualOrderSyncInProgress', $value);
    }

    /**
     * Retrieves manualOrderSyncInProgress value.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isManualOrderSyncInProgress(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('manualOrderSyncInProgress', false);
    }

    /**
     * Sets onboardingStarted value.
     *
     * @param bool $value
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setOnboardingStarted(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('onboardingStarted', $value, false);
    }

    /**
     * Retrieves onboardingStarted value.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getOnboardingStarted(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('onboardingStarted', false, false);
    }

    /**
     * Sets initialSyncInProgress flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setInitialSyncInProgress(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('initialSyncInProgress', $value);
    }

    /**
     * Checks if initial sync is In progress.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isInitialSyncInProgress(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('initialSyncInProgress', false);
    }

    /**
     * Sets accountConfigured flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAccountConfigured(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('accountConfigured', $value);
    }

    /**
     * Checks if account is configured.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isAccountConfigured(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('accountConfigured', false);
    }

    /**
     * Sets orderConfigured flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setOrderConfigured(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('orderConfigured', $value);
    }

    /**
     * Checks if order is configured.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isOrderConfigured(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('orderConfigured', false);
    }

    /**
     * Sets productConfigured flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setProductConfigured(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('productConfigured', $value);
    }

    /**
     * Checks if product is configured.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isProductConfigured(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('productConfigured', false);
    }

    /**
     * Sets productSyncInProgress flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setProductSyncInProgress(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('productSyncInProgress', $value);
    }

    /**
     * Checks if product sync is In progress.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isProductSyncInProgress(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('productSyncInProgress', false);
    }

    /**
     * Sets orderSyncInProgress flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setOrderSyncInProgress(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('orderSyncInProgress', $value);
    }

    /**
     * Checks if order sync is In progress.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isOrderSyncInProgress(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('orderSyncInProgress', false);
    }

    /**
     * Checks if onboarding is completed.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isOnboardingCompleted(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('onboardingCompleted', false);
    }

    /**
     * Sets onboardingCompleted flag.
     *
     * @param bool $value
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setOnboardingCompleted(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('onboardingCompleted', $value);
    }

    /**
     * Sets onboardingCompletedIntegrationDisabled flag.
     *
     * @param bool $value
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setOnboardingCompletedIntegrationDisabled(bool $value): void
    {
        ConfigurationManager::getInstance()->saveConfigValue('onboardingCompletedIntegrationDisabled', $value);
    }

    /**
     * Checks if onboardingCompletedIntegrationDisabled flag is set.
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isOnboardingCompletedIntegrationDisabled(): bool
    {
        return ConfigurationManager::getInstance()->getConfigValue('onboardingCompletedIntegrationDisabled', false);
    }
}
