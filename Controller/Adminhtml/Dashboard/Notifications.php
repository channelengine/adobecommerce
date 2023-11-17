<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Notifications
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard
 */
class Notifications extends Action
{
    use SetsContextTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var NotificationService
     */
    private $notificationService;
    /**
     * @var TransactionLogService
     */
    private $logService;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Retrieves notifications.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $offset = (int)$this->_request->getParam('offset');
        $limit = (int)$this->_request->getParam('limit');
        $notifications = $this->getNotificationService()->find(
            ['isRead' => false, 'context' => $this->_request->getParam('storeId')],
            $offset ?? 0,
            $limit ?? 15
        );
        $formattedNotifications = $this->formatNotifications($notifications);
        $numberOfNotifications = count($formattedNotifications);
        $totalNumberOfNotifications = $this->getNotificationService()->countNotRead();

        return $this->resultJsonFactory->create()->setData([
            'notifications' => $formattedNotifications,
            'numberOfNotifications' => $numberOfNotifications + $offset,
            'disableButton' => $totalNumberOfNotifications - ($offset + $numberOfNotifications) === 0,
        ]);
    }

    /**
     * @param Notification[] $notifications
     *
     * @return array
     */
    private function formatNotifications(array $notifications): array
    {
        $formattedNotifications = [];

        foreach ($notifications as $notification) {
            $log = $this->getLogService()->find(
                [
                    'id' => $notification->getTransactionLogId(),
                    'context' => ConfigurationManager::getInstance()->getContext(),
                ]
            )[0];

            $formattedNotifications[] = [
                'logId' => $notification->getTransactionLogId(),
                'notificationId' => $notification->getId(),
                'context' => __($notification->getNotificationContext()),
                'message' => vsprintf(__($notification->getMessage()), $notification->getArguments()),
                'date' => $log ? $log->getStartTime()->format('d/m/Y') : '',
            ];
        }

        return $formattedNotifications;
    }

    /**
     * @return NotificationService
     */
    private function getNotificationService(): NotificationService
    {
        if ($this->notificationService === null) {
            $this->notificationService = ServiceRegister::getService(NotificationService::class);
        }

        return $this->notificationService;
    }

    /**
     * @return TransactionLogService
     */
    private function getLogService(): TransactionLogService
    {
        if ($this->logService === null) {
            $this->logService = ServiceRegister::getService(TransactionLogService::class);
        }

        return $this->logService;
    }
}
