<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\Infrastructure;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\Configuration;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\LogData;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Singleton;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ConfigService;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\Infrastructure
 */
class LoggerService extends Singleton implements ShopLoggerAdapter
{
    /**
     * Singleton instance of this class.
     *
     * @var static
     */
    protected static $instance;
    /**
     * Log level names for corresponding log level codes.
     *
     * @var array
     */
    private static $logLevelName = [
        Logger::ERROR => 'error',
        Logger::WARNING => 'warning',
        Logger::INFO => 'info',
        Logger::DEBUG => 'debug',
    ];
    /**
     * Magento logger interface.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Logger service constructor.
     *
     * @param LoggerInterface $logger Magento logger interface.
     */
    public function __construct(LoggerInterface $logger, Json $serializer)
    {
        parent::__construct();

        $this->logger = $logger;
        $this->serializer = $serializer;

        static::$instance = $this;
    }

    /**
     * Logs message in the system.
     *
     * @param LogData $data
     *
     * @return void
     */
    public function logMessage(LogData $data): void
    {
        /** @var ConfigService $configService */
        $configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        $minLogLevel = $configService->getMinLogLevel();
        $logLevel = $data->getLogLevel();

        if (($logLevel > $minLogLevel) && !$configService->isDebugModeEnabled()) {
            return;
        }

        $message = 'CHANNELENGINE LOG: 
            Date: ' . date('d/m/Y') . '
            Time: ' . date('H:i:s') . '
            Log level: ' . self::$logLevelName[$logLevel] . '
            Message: ' . $data->getMessage();
        $context = $data->getContext();
        if (!empty($context)) {
            $message .= '
            Context data: [';
            foreach ($context as $item) {
                $message .= '"' . $item->getName() . '" => "' . $this->serializer->serialize($item->getValue()) . '", ';
            }

            $message .= ']';
        }

        $this->logger->{self::$logLevelName[$logLevel]}($message, $context);
    }
}
