<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\DTO\AuthInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Exceptions\CurrencyMismatchException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpClient;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationServiceInterface;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Traits\GetPostParamsTrait;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Account
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Onboarding
 */
class Account extends Action
{
    use SetsContextTrait;
    use GetPostParamsTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var TranslationServiceInterface
     */
    private $translationService;
    /**
     * @var AuthorizationService
     */
    private $authService;
    /**
     * @var WebhooksService
     */
    private $webhookService;

    /**
     * Account controller constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context               $context,
        JsonFactory           $resultJsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Connects user to ChannelEngine.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws QueryFilterInvalidParamException
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);

        $postParams = $this->getPostParams();
        $apiKey = $postParams['apiKey'] ?? '';
        $accountName = $postParams['accountName'] ?? '';
        $storeId = $postParams['storeId'];

        if (empty($apiKey) || empty($accountName) || empty($storeId)) {
            return $this->returnError('loginRequiredFields');
        }

        if ($this->isStoreAlreadyConnected()) {
            return $this->returnError('Store is already connected to ChannelEngine');
        }

        if ($this->isAccountAlreadyConnected($accountName)) {
            return $this->returnError('Another store is already connected to ChannelEngine with this account.');
        }

        try {
            $currency = $this->storeManager->getStore($storeId)->getCurrentCurrencyCode();
            $this->getAuthService()->validateAccountInfo($apiKey, $accountName, $currency);

            $orderProxy = new Proxy(ServiceRegister::getService(HttpClient::class), $accountName, $apiKey);
            $orderProxy->getNew();
            $authInfo = AuthInfo::fromArray(['account_name' => $accountName, 'api_key' => $apiKey]);
            $this->getAuthService()->setAuthInfo($authInfo);
            $this->getStateService()->setAccountConfigured(true);
            $this->getStoreService()->setStoreId($storeId);
            $this->registerWebhooks();

            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (QueryFilterInvalidParamException|CurrencyMismatchException $e) {
            return $this->returnError($e->getMessage());
        } catch (Exception $e) {
            return $this->returnError('invalidCredentials');
        }
    }

    /**
     * Registers webhooks on ChannelEngine.
     *
     * @return void
     */
    private function registerWebhooks(): void
    {
        $this->getWebhookService()->createWebhookToken();
        $this->getWebhookService()->createWebhookUniqueId();
        $this->getWebhookService()->create();
    }

    /**
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function isStoreAlreadyConnected(): bool
    {
        $authInfo = ConfigurationManager::getInstance()->getConfigValue('authInfo');

        if (!$authInfo) {
            return false;
        }

        return true;
    }

    /**
     * @param string $accountName
     *
     * @return bool
     *
     * @throws QueryFilterInvalidParamException
     */
    private function isAccountAlreadyConnected(string $accountName): bool
    {
        $authInfo = ConfigurationManager::getInstance()->getConfigValue('authInfo', false, false);

        if (!$authInfo) {
            return false;
        }

        $account = AuthInfo::fromArray($authInfo);

        return $account->getAccountName() === $accountName;
    }

    /**
     * @param string $message
     *
     * @return Json
     */
    private function returnError(string $message): Json
    {
        return $this->resultJsonFactory->create()->setData([
            'success' => false,
            'message' => $this->getTranslationService()->translate($message)
        ]);
    }

    /**
     * @return AuthorizationService
     */
    private function getAuthService(): AuthorizationService
    {
        if ($this->authService === null) {
            $this->authService = ServiceRegister::getService(AuthorizationService::class);
        }

        return $this->authService;
    }

    /**
     * @return TranslationServiceInterface
     */
    private function getTranslationService(): TranslationServiceInterface
    {
        if ($this->translationService === null) {
            $this->translationService = ServiceRegister::getService(TranslationServiceInterface::class);
        }

        return $this->translationService;
    }

    /**
     * @return WebhooksService
     */
    private function getWebhookService(): WebhooksService
    {
        if ($this->webhookService === null) {
            $this->webhookService = ServiceRegister::getService(WebhooksService::class);
        }

        return $this->webhookService;
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
}
