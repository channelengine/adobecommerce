<?php

namespace ChannelEngine\ChannelEngineIntegration\Utility;

use ChannelEngine\ChannelEngineIntegration\Api\ReturnsServiceFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\Entity\ReturnData;
use ChannelEngine\ChannelEngineIntegration\Events\NotificationCreatedEvent;
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
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\SupportConsole\Contracts\SupportService as SupportServiceInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Utility\Listeners\SystemCleanupListener;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
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
use ChannelEngine\ChannelEngineIntegration\Repository\BaseRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\LogRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\ProductEventRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\QueueItemRepository;
use ChannelEngine\ChannelEngineIntegration\Repository\ReturnDataRepository;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsTypesService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\CancellationService as IntegrationCancellationService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ConfigService;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationService;
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
use ChannelEngine\ChannelEngineIntegration\Services\Infrastructure\LoggerService;
use Magento\Framework\Notification\NotifierPool;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts\ShipmentsService as BaseShipmentsService;

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
        ReturnsServiceFactoryInterface $returnsServiceFactory
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
            TranslationService::class,
            static function () {
                return new \ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\TranslationService();
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
    }

    /**
     * @return void
     *
     * @throws RepositoryClassException
     */
    private function initRepositories(): void
    {
        RepositoryRegistry::registerRepository(ConfigEntity::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(Process::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(LogData::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(QueueItem::getClassName(), QueueItemRepository::getClassName());
        RepositoryRegistry::registerRepository(Details::getClassName(), LogRepository::getClassName());
        RepositoryRegistry::registerRepository(TransactionLog::getClassName(), LogRepository::getClassName());
        RepositoryRegistry::registerRepository(Notification::getClassName(), LogRepository::getClassName());
        RepositoryRegistry::registerRepository(ProductEvent::getClassName(), ProductEventRepository::getClassName());
        RepositoryRegistry::registerRepository(OrdersConfigEntity::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(OrdersChannelSupportEntity::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(ReturnData::getClassName(), ReturnDataRepository::getClassName());
        RepositoryRegistry::registerRepository(ReturnsConfigEntity::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(SyncConfig::getClassName(), BaseRepository::getClassName());
        RepositoryRegistry::registerRepository(OrderSyncConfig::getClassName(), BaseRepository::getClassName());
    }

    /**
     * @return void
     */
    private function initEvents(): void
    {
        EventBus::getInstance()->when(
            QueueStatusChangedEvent::class,
            [ProductStateTransitionListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            QueueStatusChangedEvent::class,
            [OrderStateTransitionListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            TickEvent::class,
            [TickEventListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            NotificationCreatedEvent::class,
            [NotificationCreatedListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            TickEvent::class,
            [SystemCleanupListener::class, 'handle']
        );

        EventBus::getInstance()->when(
            TickEvent::class,
            [SalePricesListener::class, 'handle']
        );
    }
}
