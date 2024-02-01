<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Transactions;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\TransactionLogService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use DateTime;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class TransactionLogs
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Transactions
 */
class TransactionLogs extends Action
{
    use SetsContextTrait;

    /**
     * @var TransactionLogService
     */
    private $logService;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
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
     * Retrieves transaction logs.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $status = (bool)$this->_request->getParam('status');
        $page = (int)$this->_request->getParam('page') ?: 1;
        $pageSize = (int)$this->_request->getParam('page_size') ?: 10;
        $taskType = $status ? '' : $this->_request->getParam('task_type');

        $logs = $this->getLogs($page, $pageSize, $taskType, $status);
        $numberOfLogs = $this->getLogService()->count($this->getFilters($taskType, $status));

        return $this->resultJsonFactory->create()->setData(
            [
                'logs' => $this->formatLogs($logs),
                'numberOfLogs' => $numberOfLogs,
                'from' => ($numberOfLogs === 0) ? 0 : ($page - 1) * $pageSize + 1,
                'to' => ($numberOfLogs < $page * $pageSize) ? $numberOfLogs : $page * $pageSize,
                'numberOfPages' => ceil($numberOfLogs / $pageSize),
                'currentPage' => (int)$page,
                'taskType' => $status ? 'Errors' : $taskType,
            ]
        );
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @param string $taskType
     * @param string $status
     *
     * @return TransactionLog[]
     */
    private function getLogs(int $page, int $pageSize, string $taskType, string $status): array
    {
        return $this->getLogService()->find(
            $this->getFilters($taskType, $status),
            ($page - 1) * $pageSize,
            $pageSize
        );
    }

    /**
     * @param string $taskType
     * @param string $status
     *
     * @return array
     */
    private function getFilters(string $taskType = '', string $status = ''): array
    {
        $filters = [];

        if ($taskType) {
            $filters['taskType'] = $taskType;
        }

        if ($taskType === 'ProductSync') {
            $filters['taskType'] = ['ProductSync', 'ProductsPurgeTask', 'ProductsUpsertTask', 'ProductsResyncTask', 'ProductsReplaceTask'];
        }

        if ($status) {
            $filters['status'] = 'failed';
        }

        $filters['context'] = $this->_request->getParam('storeId');

        return $filters;
    }

    /**
     * @param TransactionLog[] $logs
     *
     * @return array
     */
    private function formatLogs(array $logs): array
    {
        $formattedLogs = [];

        foreach ($logs as $log) {
            $detail = $this->getDetailsService()->getForLog($log->getId());

            $formattedLog = [
                'taskType' => __($log->getTaskType()),
                'status' => __($log->getStatus()),
                'startTime' => '',
                'completedTime' => '',
                'id' => $log->getId(),
                'hasDetails' => $detail !== [],
            ];

            if ($log->getStartTime()) {
                $formattedLog['startTime'] = (new DateTime())
                    ->setTimestamp($log->getStartTime()->getTimestamp())
                    ->format('d/m/Y H.i');
            }

            if ($log->getCompletedTime()) {
                $formattedLog['completedTime'] = (new DateTime())
                    ->setTimestamp($log->getCompletedTime()->getTimestamp())
                    ->format('d/m/Y H.i');
            }

            $formattedLogs[] = $formattedLog;
        }

        return $formattedLogs;
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
