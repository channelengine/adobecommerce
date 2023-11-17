<?php

namespace ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\ExtraData;

use ChannelEngine\ChannelEngineIntegration\Exceptions\ContextNotSetException;
use ChannelEngine\ChannelEngineIntegration\Traits\SetsContextTrait;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class GetAttributes
 *
 * @package ChannelEngine\ChannelEngineIntegration\Controller\Adminhtml\ExtraData
 */
class GetAttributes extends Action
{
    use SetsContextTrait;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var CollectionFactory
     */
    private $productAttributes;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $productAttributes
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, CollectionFactory $productAttributes)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productAttributes = $productAttributes;
    }

    /**
     * Retrieves product attributes.
     *
     * @return Json
     *
     * @throws ContextNotSetException
     */
    public function execute(): Json
    {
        $this->setContext($this->_request);
        $attributes = [];

        $collection = $this->productAttributes->create()
            ->addFieldToFilter(
                'main_table.frontend_input',
                ['in' => ['select', 'text', 'textarea', 'date', 'datetime', 'boolean', 'multiselect', 'weight']]
            )->addStoreLabel($this->_request->getParam('storeId'));

        foreach ($collection as $item) {
            if ($item->getStoreLabel()) {
                $attributes[] = [
                    'value' => $item->getAttributeCode(),
                    'label' => $item->getStoreLabel(),
                ];
            }
        }

        return $this->resultJsonFactory->create()->setData(
            [
                'product_attributes' => $attributes,
            ]
        );
    }
}
