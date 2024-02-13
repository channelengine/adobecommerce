<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details as DetailEntity;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Details
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\Dashboard
 */
class Details extends Action
{
    use SetsContextTrait;

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
     * Retrieves details.
     *
     * @return ResponseInterface|Json|ResultInterface
     *
     * @throws ContextNotSetException
     */
    public function execute()
    {
        $this->setContext($this->_request);
        $logId = (int)$this->_request->getParam('log_id');
        $page = (int)$this->_request->getParam('page') ?: 1;
        $pageSize = $this->_request->getParam('page_size') ?: 10;
        $details = $this->getDetailsService()->find(
            ['logId' => $logId, 'context' => $this->_request->getParam('storeId')],
            ($page - 1) * $pageSize,
            $pageSize
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
                'from' => ($numberOfDetails === 0) ? 0 : ($page - 1) * $pageSize + 1,
                'to' => ($numberOfDetails < $page * $pageSize) ? $numberOfDetails : $page * $pageSize,
                'numberOfPages' => ceil($numberOfDetails / $pageSize),
                'currentPage' => (int)$page,
                'logId' => $logId,
                'pageSize' => $pageSize,
            ]
        );
    }

    /**
     * @param DetailEntity[] $details
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
