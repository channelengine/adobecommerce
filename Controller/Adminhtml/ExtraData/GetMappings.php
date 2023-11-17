<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\ExtraData;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExtraDataAttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Get
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\ExtraData
 */
class GetMappings extends Action
{
    use SetsContextTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var ExtraDataAttributeMappingsService
     */
    private $extraDataMappingsService;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ExtraDataAttributeMappingsService $extraDataMappingsService
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ExtraDataAttributeMappingsService $extraDataMappingsService
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->extraDataMappingsService = $extraDataMappingsService;
    }

    /**
     * Retrieves extra data attribute mappings.
     *
     * @return Json
     *
     * @throws ContextNotSetException
     * @throws QueryFilterInvalidParamException
     */
    public function execute(): Json
    {
        $this->setContext($this->_request);

        $extraDataMappings = $this->extraDataMappingsService->getExtraDataAttributeMappings();

        return $this->resultJsonFactory->create()->setData(
            [
                'extra_data_mapping' => $extraDataMappings ? $extraDataMappings->getMappings() : [],
            ]
        );
    }
}
