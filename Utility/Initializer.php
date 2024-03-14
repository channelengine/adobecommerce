<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Utility;

use ChannelEngine\ChannelEngineIntegration\Api\ReturnsServiceFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\Entity\ReturnData;
use ChannelEngine\ChannelEngineIntegration\Events\NotificationCreatedEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts\AuthorizationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\BootstrapComponent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Contracts\CancellationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\OrdersChannelSupportEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService as CoreOrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrderSyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Contracts\OrdersService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\ProductEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Entities\SyncConfig;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Configuration\ReturnsConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Configuration\ReturnsConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts\ReturnsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts\ShipmentsService as BaseShipmentsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole\Contracts\SupportService as SupportServiceInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Utility\Listeners\SystemCleanupListener;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpClient;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\LogData;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Concrete\JsonSerializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Events\QueueStatusChangedEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Process;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\QueueItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\TaskEvents\TickEvent;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\Events\EventBus;
use ChannelEngine\ChannelEngineIntegration\Listeners\Notifications\NotificationCreatedListener;
use ChannelEngine\ChannelEngineIntegration\Listeners\Products\SalePricesListener;
use ChannelEngine\ChannelEngineIntegration\Listeners\Products\TickEventListener;
use ChannelEngine\ChannelEngineIntegration\Listeners\StateTransition\OrderStateTransitionListener;
use ChannelEngine\ChannelEngineIntegration\Listeners\StateTransition\ProductStateTransitionListener;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineEntityFactory;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\LogEntityFactory;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ProductEventFactory;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\QueueItemEntityFactory;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ReturnDataFactory;
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\LogRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\ProductEventRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\QueueItemRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\ReturnDataRepository;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsTypesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\CancellationService as IntegrationCancellationService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ConfigService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationServiceInterface;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\DisconnectService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExportProductsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExtraDataAttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\InitialSyncStateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\OrderService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PluginStatusService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\PriceSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products\ProductSalePricesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products\ProductService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ReturnsSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\SalesPricesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ShipmentsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StateService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StockSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\StoreService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\SupportService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ThreeLevelSyncSettingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\TranslationService;
use ChannelEngine\ChannelEngineIntegration\Services\Infrastructure\LoggerService;
use ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper\OrderProxy;
use ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper\ShipmentProxy;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Notification\NotifierPool;

/**
 * Class Initializer
 *
 * @package ChannelEngine\ChannelEngineIntegration\Utility
 */
class Initializer
{
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var SupportService
     */
    private $supportService;
    /**
     * @var LoggerService
     */
    private $loggerService;
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var DisconnectService
     */
    private $disconnectService;
    /**
     * @var NotifierPool
     */
    private $notifierPool;
    /**
     * @var OrderService
     */
    private $orderService;
    /**
     * @var IntegrationCancellationService
     */
    private $cancellationService;
    /**
     * @var \ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\WebhooksService
     */
    private $webhooksService;
    /**
     * @var ShipmentsService
     */
    private $shipmentsService;
    /**
     * @var SalesPricesService
     */
    private $salesPricesService;
    /**
     * @var ReturnsServiceFactoryInterface
     */
    private $returnsServiceFactory;

    /**
     * @var ChannelEngineEntityFactory
     */
    private $channelEngineEntityFactory;

    /**
     * @var QueueItemEntityFactory
     */
    private $queueItemEntityFactory;

    /**
     * @var ProductEventFactory
     */
    private $productEventFactory;

    /**
     * @var LogEntityFactory
     */
    private $logEntityFactory;

    /**
     * @var ReturnDataFactory
     */
    private $returnDataFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var OrderProxy
     */
    private $orderProxy;

    /**
     * @var ShipmentProxy
     */
    private $shipmentProxy;

    /**
     * @param ConfigService $configService
     * @param SupportService $supportService
     * @param LoggerService $loggerService
     * @param ProductService $productService
     * @param DisconnectService $disconnectService
     * @param NotifierPool $notifierPool
     * @param OrderService $orderService
     * @param IntegrationCancellationService $cancellationService
     * @param \ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\WebhooksService $webhooksService
     * @param ShipmentsService $shipmentsService
     * @param SalesPricesService $salesPricesService
     * @param ReturnsServiceFactoryInterface $returnsServiceFactory
     * @param ChannelEngineEntityFactory $channelEngineEntityFactory
     * @param QueueItemEntityFactory $queueItemEntityFactory
     * @param ProductEventFactory $productEventFactory
     * @param LogEntityFactory $logEntityFactory
     * @param ReturnDataFactory $returnDataFactory
     * @param Session $session
     */
    public function __construct(
        ConfigService                  $configService,
        SupportService                 $supportService,
        LoggerService                  $loggerService,
        ProductService                 $productService,
        DisconnectService              $disconnectService,
        NotifierPool                   $notifierPool,
        OrderService                   $orderService,
        IntegrationCancellationService $cancellationService,
        \ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\WebhooksService $webhooksService,
        ShipmentsService $shipmentsService,
        SalesPricesService $salesPricesService,
        ReturnsServiceFactoryInterface $returnsServiceFactory,
        ChannelEngineEntityFactory  $channelEngineEntityFactory,
        QueueItemEntityFactory  $queueItemEntityFactory,
        ProductEventFactory  $productEventFactory,
        LogEntityFactory  $logEntityFactory,
        ReturnDataFactory  $returnDataFactory,
        Session $session
    ) {
        $this->configService = $configService;
        $this->supportService = $supportService;
        $this->loggerService = $loggerService;
        $this->productService = $productService;
        $this->disconnectService = $disconnectService;
        $this->notifierPool = $notifierPool;
        $this->orderService = $orderService;
        $this->cancellationService = $cancellationService;
        $this->webhooksService = $webhooksService;
        $this->shipmentsService = $shipmentsService;
        $this->salesPricesService = $salesPricesService;
        $this->returnsServiceFactory = $returnsServiceFactory;
        $this->channelEngineEntityFactory = $channelEngineEntityFactory;
        $this->queueItemEntityFactory = $queueItemEntityFactory;
        $this->productEventFactory = $productEventFactory;
        $this->logEntityFactory = $logEntityFactory;
        $this->returnDataFactory = $returnDataFactory;
        $this->session = $session;
        ServiceRegister::registerService(Initializer::class, function (){
            return $this;
        });
    }

    /**
     * Initializes services, repositories, events and proxies.
     *
     * @return void
     *
     * @throws RepositoryClassException
     */
    public function init(): void
    {
        BootstrapComponent::init();

        $this->initRepositories();
        $this->initServices();
        $this->initEvents();
    }

    private function initServices(): void
    {
        ServiceRegister::registerService(
            Configuration::class,
            function () {
                return $this->configService;
            }
        );

        ServiceRegister::registerService(
            Serializer::class,
            static function () {
                return new JsonSerializer();
            }
        );

        ServiceRegister::registerService(
            ShopLoggerAdapter::class,
            function () {
                return $this->loggerService;
            }
        );

        ServiceRegister::registerService(
            SupportServiceInterface::class,
            function () {
                return $this->supportService;
            }
        );

        ServiceRegister::registerService(
            StateService::class,
            static function () {
                return new StateService();
            }
        );

        ServiceRegister::registerService(
            TranslationServiceInterface::class,
            function () {
                return new TranslationService($this->session);
            }
        );

        ServiceRegister::registerService(
            StoreService::class,
            static function () {
                return new StoreService();
            }
        );

        ServiceRegister::registerService(
            PriceSettingsService::class,
            static function () {
                return new PriceSettingsService();
            }
        );

        ServiceRegister::registerService(
            StockSettingsService::class,
            static function () {
                return new StockSettingsService();
            }
        );

        ServiceRegister::registerService(
            ThreeLevelSyncSettingsService::class,
            static function () {
                return new ThreeLevelSyncSettingsService();
            }
        );

        ServiceRegister::registerService(
            AttributeMappingsService::class,
            static function () {
                return new AttributeMappingsService();
            }
        );

        ServiceRegister::registerService(
            AttributeMappingsTypesService::class,
            static function () {
                return new AttributeMappingsTypesService();
            }
        );

        ServiceRegister::registerService(
            CoreOrdersConfigurationService::class,
            static function () {
                return new OrdersConfigurationService();
            }
        );

        ServiceRegister::registerService(
            ProductsService::class,
            function () {
                return $this->productService;
            }
        );

        ServiceRegister::registerService(
            NotificationService::class,
            static function () {
                return new \ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\NotificationService();
            }
        );

        ServiceRegister::registerService(
            TransactionLogService::class,
            static function () {
                return new \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\TransactionLogService();
            }
        );

        ServiceRegister::registerService(
            PluginStatusService::class,
            static function () {
                return new PluginStatusService();
            }
        );

        ServiceRegister::registerService(
            DisconnectService::class,
            function () {
                return $this->disconnectService;
            }
        );

        ServiceRegister::registerService(
            NotifierPool::class,
            function () {
                return $this->notifierPool;
            }
        );

        ServiceRegister::registerService(
            OrdersService::class,
            function () {
                return $this->orderService;
            }
        );

        ServiceRegister::registerService(
            CancellationService::class,
            function () {
                return $this->cancellationService;
            }
        );

        ServiceRegister::registerService(
            WebhooksService::class,
            function () {
                return $this->webhooksService;
            }
        );

        ServiceRegister::registerService(
            BaseShipmentsService::class,
            function () {
                return $this->shipmentsService;
            }
        );

        ServiceRegister::registerService(
            ReturnsSettingsService::class,
            static function () {
                return new ReturnsSettingsService();
            }
        );

        ServiceRegister::registerService(
            ReturnsService::class,
            function () {
                return $this->returnsServiceFactory->create();
            }
        );

        ServiceRegister::registerService(
            ReturnsConfigurationService::class,
            static function () {
                return new ReturnsConfigurationService();
            }
        );

        ServiceRegister::registerService(
            ProductSalePricesService::class,
            static function () {
                return new ProductSalePricesService();
            }
        );

        ServiceRegister::registerService(
            InitialSyncStateService::class,
            static function () {
                return new InitialSyncStateService();
            }
        );

        ServiceRegister::registerService(
            SalesPricesService::class,
            function () {
                return $this->salesPricesService;
            }
        );

        ServiceRegister::registerService(
            ExtraDataAttributeMappingsService::class,
            static function () {
                return new ExtraDataAttributeMappingsService();
            }
        );

        ServiceRegister::registerService(
            ExportProductsService::class,
            static function () {
                return new ExportProductsService();
            }
        );

        ServiceRegister::registerService(
            ChannelEngineEntityFactory::class,
            function () {
                return $this->channelEngineEntityFactory;
            }
        );

        ServiceRegister::registerService(
            QueueItemEntityFactory::class,
            function () {
                return $this->queueItemEntityFactory;
            }
        );

        ServiceRegister::registerService(
            ProductEventFactory::class,
            function () {
                return $this->productEventFactory;
            }
        );

        ServiceRegister::registerService(
            LogEntityFactory::class,
            function () {
                return $this->logEntityFactory;
            }
        );

        ServiceRegister::registerService(
            ReturnDataFactory::class,
            function () {
                return $this->returnDataFactory;
            }
        );

        ServiceRegister::registerService(
            TranslationServiceInterface::class,
            function () {
                return new TranslationService($this->session);
            }
        );
        ServiceRegister::registerService(
            Proxy::CLASS_NAME,
            function () {
                if($this->orderProxy) {
                    return $this->orderProxy;
                }

                $orderProxyClass = Proxy::class;
                /** @var ConfigurationManager $configManager */
                $configManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);
                if ($diClass = $configManager->getConfigValue('di_order_proxy_class', null, false)) {
                    $orderProxyClass = $diClass;
                }
                /** @var HttpClient $httpClient */
                $httpClient = ServiceRegister::getService(HttpClient::CLASS_NAME);
                /** @var AuthorizationService $authService */
                $authService = ServiceRegister::getService(AuthorizationService::CLASS_NAME);
                $authInfo = $authService->getAuthInfo();
                $this->orderProxy = new $orderProxyClass($httpClient, $authInfo->getAccountName(), $authInfo->getApiKey());

                return $this->orderProxy;
            }
        );


        ServiceRegister::registerService(
            \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\Http\Proxy::class,
            function () {
                if($this->shipmentProxy) {
                    return $this->shipmentProxy;
                }

                $shipmentProxyClass = \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\Http\Proxy::class;
                /** @var ConfigurationManager $configManager */
                $configManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);
                if ($diClass = $configManager->getConfigValue('di_shipment_proxy_class', null, false)) {
                    $shipmentProxyClass = $diClass;
                }
                /** @var HttpClient $httpClient */
                $httpClient = ServiceRegister::getService(HttpClient::CLASS_NAME);
                /** @var AuthorizationService $authService */
                $authService = ServiceRegister::getService(AuthorizationService::CLASS_NAME);
                $authInfo = $authService->getAuthInfo();
                $this->shipmentProxy = new $shipmentProxyClass($httpClient, $authInfo->getAccountName(), $authInfo->getApiKey());

                return $this->shipmentProxy;
            }
        );
    }

    /**
     * @return void
     *
     * @throws RepositoryClassException
     */
    private function initRepositories(): void
    {
        RepositoryRegistry::registerRepository(ConfigEntity::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(Process::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(LogData::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(QueueItem::getClassName(), QueueItemRepository::class);
        RepositoryRegistry::registerRepository(Details::getClassName(), LogRepository::class);
        RepositoryRegistry::registerRepository(TransactionLog::getClassName(), LogRepository::class);
        RepositoryRegistry::registerRepository(Notification::getClassName(), LogRepository::class);
        RepositoryRegistry::registerRepository(ProductEvent::getClassName(), ProductEventRepository::class);
        RepositoryRegistry::registerRepository(OrdersConfigEntity::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(OrdersChannelSupportEntity::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(ReturnData::getClassName(), ReturnDataRepository::class);
        RepositoryRegistry::registerRepository(ReturnsConfigEntity::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(SyncConfig::getClassName(), BaseRepository::class);
        RepositoryRegistry::registerRepository(OrderSyncConfig::getClassName(), BaseRepository::class);
    }

    /**
     * @return void
     */
    private function initEvents(): void
    {
        EventBus::getInstance()->when(
            QueueStatusChangedEvent::class,
            [new ProductStateTransitionListener(), 'handle']
        );

        EventBus::getInstance()->when(
            QueueStatusChangedEvent::class,
            [new OrderStateTransitionListener(), 'handle']
        );

        EventBus::getInstance()->when(
            TickEvent::class,
            [TickEventListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            NotificationCreatedEvent::class,
            [new NotificationCreatedListener(), 'handle']
        );

        EventBus::getInstance()->when(
            TickEvent::class,
            [SystemCleanupListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            TickEvent::class,
            [new SalePricesListener(), 'handle']
        );
    }
}
