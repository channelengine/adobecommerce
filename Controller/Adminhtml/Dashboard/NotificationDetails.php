<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class NotificationDetails
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard
 */
class NotificationDetails extends Action
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
     * @var DetailsService
     */
    private $detailsService;

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
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $notificationId = (int)$this->_request->getParam('notificationId');
        $logId = (int)$this->_request->getParam('logId');

        $notification = $this->getNotificationsService()->get($notificationId);

        if ($notification) {
            $this->getNotificationsService()->delete($notification);
        }

        $details = $this->getDetailsService()->find(
            [
                'logId' => $logId,
                'context' => $this->_request->getParam('storeId')
            ],
            0,
            10
        );
        $numberOfDetails = $this->getDetailsService()->count(
            [
                'logId' => $logId,
                'context' => $this->_request->getParam('storeId')
            ]
        );

        return $this->resultJsonFactory->create()->setData(
            [
                'details' => $this->formatDetails($details),
                'numberOfDetails' => $numberOfDetails,
                'from' => ($numberOfDetails === 0) ? 0 : 1,
                'to' => ($numberOfDetails < 10) ? $numberOfDetails : 10,
                'numberOfPages' => ceil($numberOfDetails / 10),
                'currentPage' => 1,
                'logId' => $logId,
                'pageSize' => 10,
            ]
        );
    }

    /**
     * @param Details[] $details
     *
     * @return array
     */
    private function formatDetails(array $details): array
    {
        $formattedDetails = [];

        foreach ($details as $detail) {
            $formattedDetails[] = [
                'message' => vsprintf(__($detail->getMessage()), $detail->getArguments()),
                'identifier' => $detail->getArguments()[0],
            ];
        }

        return $formattedDetails;
    }

    /**
     * @return NotificationService
     */
    private function getNotificationsService(): NotificationService
    {
        if ($this->notificationService === null) {
            $this->notificationService = ServiceRegister::getService(NotificationService::class);
        }

        return $this->notificationService;
    }

    /**
     * @return DetailsService
     */
    private function getDetailsService(): DetailsService
    {
        if ($this->detailsService === null) {
            $this->detailsService = ServiceRegister::getService(DetailsService::class);
        }

        return $this->detailsService;
    }
}
