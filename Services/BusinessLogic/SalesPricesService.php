<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class SalesPricesService
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieves product ids for products with sales prices.
     *
     * @return array
     */
    public function getProductsWithSalesPricesIds(): array
    {
        $saleStartsIds = $this->collectionFactory->create()->addFieldToFilter('special_price', ['neq' => ''])
            ->addAttributeToFilter([
                [
                    'attribute' => 'special_from_date',
                    'eq' => date('Y-m-d G:i:s', strtotime(date('Y-m-d'))),
                    'date' => true
                ]
            ])->getAllIds();

        $saleEndsIds = $this->collectionFactory->create()->addFieldToFilter('special_price', ['neq' => ''])
            ->addAttributeToFilter([
                [
                    'attribute' => 'special_to_date',
                    'eq' => (date('Y-m-d G:i:s', strtotime(date('Y-m-d')))),
                    'date' => true
                ]
            ])->getAllIds();

        return array_merge($saleEndsIds, $saleStartsIds);
    }
}
