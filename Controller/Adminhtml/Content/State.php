<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Interfaces\TaskRunnerWakeup;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class State
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Content
 */
class State extends Action
{
    use SetsContextTrait;

    private const MAPPINGS = [
        StateService::WELCOME_STATE => [
            'currentAction' => 'welcome',
            'route' => 'channelengine/content/welcome',
        ],
        StateService::ACCOUNT_CONFIGURATION => [
            'currentAction' => 'account',
            'route' => 'channelengine/content/account',
        ],
        StateService::PRODUCT_CONFIGURATION => [
            'currentAction' => 'productsync',
            'route' => 'channelengine/content/productsync',
        ],
        StateService::ORDER_SETTINGS => [
            'currentAction' => 'ordersettings',
            'route' => 'channelengine/content/ordersettings',
        ],
        StateService::ENABLE_AND_SYNC => [
            'currentAction' => 'initialsync',
            'route' => 'channelengine/content/initialsync',
        ],
        StateService::DASHBOARD => [
            'currentAction' => 'dashboard',
            'route' => 'channelengine/content/dashboard',
        ],
        StateService::CONFIG => [
            'currentAction' => 'config',
            'route' => 'channelengine/content/config',
        ],
        StateService::TRANSACTIONS => [
            'currentAction' => 'transactions',
            'route' => 'channelengine/content/transactions',
        ],
    ];

    private const VALID_PREVIOUS_PAGES = [
        StateService::PRODUCT_CONFIGURATION => [
            StateService::ACCOUNT_CONFIGURATION,
        ],
        StateService::ORDER_SETTINGS => [
            StateService::ACCOUNT_CONFIGURATION,
            StateService::PRODUCT_CONFIGURATION,
        ],
        StateService::ENABLE_AND_SYNC => [
            StateService::ACCOUNT_CONFIGURATION,
            StateService::PRODUCT_CONFIGURATION,
            StateService::ORDER_SETTINGS,
        ]
    ];

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ResultFactory
     */
    private $resultResponseFactory;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * Initialize State controller.
     *
     * @param Context $context
     * @param Http $request
     * @param PageFactory $resultPageFactory
     * @param ResultFactory $resultFactory
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        Context     $context,
        Http        $request,
        PageFactory $resultPageFactory,
        ResultFactory $resultFactory,
        UrlInterface $backendUrl
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultResponseFactory = $resultFactory;
        $this->backendUrl = $backendUrl;
    }

    /**
     * Returns current state of the plugin.
     *
     * @return ResultInterface|Page
     *
     * @throws QueryFilterInvalidParamException
     */
    public function execute()
    {
        $this->getTaskRunnerWakeupService()->wakeup();

        if (($redirectResponse = $this->redirectHandler()) !== true) {
            return $redirectResponse;
        }


        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();
        $resultPage->getConfig()->getTitle()->prepend('ChannelEngine');
        $resultPage->setActiveMenu('ChannelEngine_ChannelEngineIntegration::channelengine_menu');

        return $resultPage;
    }

    /**
     * @return bool|Redirect|ResultInterface|(ResultInterface&Redirect)
     *
     * @throws QueryFilterInvalidParamException
     */
    private function redirectHandler()
    {
        try {
            $this->setContext($this->request);
        } catch (ContextNotSetException $e) {
            ConfigurationManager::getInstance()->setContext($this->getStoreService()->getFirstConnectedStoreId());
        }

        if ($this->_request->getParam('redirected')) {
            return true;
        }

        $state = $this->getStateService()->getCurrentState();
        $page = $this->request->get('page');

        if (empty($page) || $page === StateService::DASHBOARD) {
            return $this->redirectIfNecessary(self::MAPPINGS[$state]['currentAction'], self::MAPPINGS[$state]['route']);
        }

        if ($state === StateService::DASHBOARD && $page === StateService::CONFIG
            && $this->getPluginStatusService()->isEnabled()) {
            return $this->redirectIfNecessary(
                self::MAPPINGS[StateService::CONFIG]['currentAction'],
                self::MAPPINGS[StateService::CONFIG]['route']
            );

        }

        if ($state === StateService::DASHBOARD && $page === StateService::TRANSACTIONS) {
            return $this->redirectIfNecessary(
                self::MAPPINGS[StateService::TRANSACTIONS]['currentAction'],
                self::MAPPINGS[StateService::TRANSACTIONS]['route']
            );
        }

        if (isset(self::VALID_PREVIOUS_PAGES[$state]) && in_array($page, self::VALID_PREVIOUS_PAGES[$state], true)) {
            $this->removeConfigs($page);
            return $this->redirectIfNecessary(
                self::MAPPINGS[$page]['currentAction'],
                self::MAPPINGS[$page]['route']
            );
        }

        if (!empty(self::MAPPINGS[$state])) {
            return $this->redirectIfNecessary(
                self::MAPPINGS[$state]['currentAction'],
                self::MAPPINGS[$state]['route']
            );
        }

        return false;
    }

    /**
     * @param string $currentAction
     * @param string $redirectUrl
     *
     * @return Redirect|ResultInterface|(Redirect&ResultInterface)|bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function redirectIfNecessary(string $currentAction, string $redirectUrl)
    {
        $actionName = $this->request->getActionName();

        if ($actionName !== $currentAction) {
            $storeId = $this->request->getParam('storeId') ?? $this->getStoreService()->getFirstConnectedStoreId();
            $redirect = $this->resultResponseFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl($this->backendUrl->getUrl($redirectUrl, ['storeId' => $storeId, 'redirected' => true]));
            return $redirect;
        }

        return true;
    }

    /**
     * @param string $page
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    private function removeConfigs(string $page): void
    {
        if ($page === StateService::ACCOUNT_CONFIGURATION) {
            $this->disconnectAccount();
            $this->getStateService()->setAccountConfigured(false);
            $this->getStateService()->setProductConfigured(false);
            $this->getStateService()->setOrderConfigured(false);
        }

        if ($page === StateService::PRODUCT_CONFIGURATION) {
            $this->getStateService()->setProductConfigured(false);
            $this->getStateService()->setOrderConfigured(false);
        }

        if ($page === StateService::ORDER_SETTINGS) {
            $this->getStateService()->setOrderConfigured(false);
        }
    }

    /**
     * @return void
     */
    private function disconnectAccount(): void
    {
        try {
            $this->getWebhookService()->delete();
            ConfigurationManager::getInstance()->saveConfigValue('authInfo', []);
        } catch (QueryFilterInvalidParamException $e) {
            Logger::logError('Failed to disconnect account because ' . $e->getMessage());
        }
    }

    /**
     * @return WebhooksService
     */
    private function getWebhookService(): WebhooksService
    {
        return ServiceRegister::getService(WebhooksService::class);
    }

    /**
     * @return StateService
     */
    private function getStateService(): StateService
    {
        return ServiceRegister::getService(StateService::class);
    }

    /**
     * @return StoreService
     */
    private function getStoreService(): StoreService
    {
        return ServiceRegister::getService(StoreService::class);
    }

    /**
     * @return PluginStatusService
     */
    private function getPluginStatusService(): PluginStatusService
    {
        return ServiceRegister::getService(PluginStatusService::class);
    }

    /**
     * @return TaskRunnerWakeup
     */
    private function getTaskRunnerWakeupService(): TaskRunnerWakeup
    {
        return ServiceRegister::getService(TaskRunnerWakeup::class);
    }
}
