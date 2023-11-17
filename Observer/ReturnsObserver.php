<?php

namespace ChannelEngine\ChannelEngineIntegration\Observer;

use ChannelEngine\ChannelEngineIntegration\Api\ReturnsServiceFactoryInterface;
use ChannelEngine\ChannelEngineIntegration\Entity\ReturnData;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\Line;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnLine;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\MerchantReturnUpdate;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts\ReturnsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ChannelEngineOrder;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder\CollectionFactory;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\AttributeMappingsService;
use ChannelEngine\ChannelEngineIntegration\Utility\Initializer;
use DateTime;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Rma\Model\Rma;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class ReturnsObserver
 *
 * @package ChannelEngine\ChannelEngineIntegration\Observer
 */
class ReturnsObserver implements ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ReturnsServiceFactoryInterface
     */
    private $returnsServiceFactory;
    /**
     * @var Initializer
     */
    private $initializer;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CollectionFactory $collectionFactory
     * @param ReturnsServiceFactoryInterface $returnsServiceFactory
     * @param Initializer $initializer
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $collectionFactory,
        ReturnsServiceFactoryInterface $returnsServiceFactory,
        Initializer $initializer
    ) {
        $this->orderRepository = $orderRepository;
        $this->collectionFactory = $collectionFactory;
        $this->returnsServiceFactory = $returnsServiceFactory;
        $this->initializer = $initializer;
    }

    /**
     * Handles rma save event.
     *
     * @param Observer $observer
     *
     * @return void
     *
     * @throws Exception
     */
    public function execute(Observer $observer): void
    {
        $this->initializer->init();

        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = ObjectManager::getInstance()->get(ProductMetadataInterface::class);
        $edition = $productMetadata->getEdition();
        if ($edition === 'Community') {
            return;
        }

        /** @var Rma $rma */
        $rma = $observer->getEvent()->getRma();
        ConfigurationManager::getInstance()->setContext((string)$rma->getStoreId());
        $orderSyncConfig = $this->getOrderSettingsService()->getOrderSyncConfig();

        if (!$orderSyncConfig || !$orderSyncConfig->isEnableReturnsSync()) {
            return;
        }

        $orderData = $this->getOrderExtension($rma);
        $returnData = $this->getReturnData($rma);

        if ($orderData && $rma->getStatus() === 'processed_closed') {
            $this->createReturn($rma);

            return;
        }

        if (!$returnData || !in_array($rma->getStatus(), ['closed', 'processed_closed'], true)) {
            return;
        }

        $this->updateReturn($rma, $returnData);
    }

    /**
     * @param Rma $rma
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     * @throws Exception
     */
    private function createReturn(Rma $rma): void
    {
        $mappings = $this->getMappingsService()->getAttributeMappings();
        $items = $rma->getItems();
        $order = $rma->getOrder();
        $lines = [];

        if (!$order) {
            return;
        }

        foreach ($items as $item) {
            $line = new MerchantReturnLine();
            $productNo = '';

            foreach ($order->getItems() as $orderItem) {
                if ((int)$orderItem->getItemId() === (int)$item->getOrderItemId()) {
                    /** @noinspection NestedTernaryOperatorInspection */
                    $productNo = ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_ID) ?
                        $orderItem->getProductId() : ($orderItem->getProduct() ? $orderItem->getProduct()->getSku() : '');
                }
            }

            if (!$productNo) {
                return;
            }

            $line->setMerchantProductNo($productNo);
            $line->setQuantity($item->getQtyRequested());
            $lines[] = $line;
        }

        $commentText = '';

        foreach ($rma->getComments() as $comment) {
            $commentText .= $comment->getComment();
        }

        $date = $rma->getData()['date_requested'];
        $returnDate = (new DateTime($date))->format('Y-m-d\TH:i:s.u\Z');

        $createRequest = MerchantReturnRequest::fromArray(
            [
                'MerchantOrderNo' => $order->getId(),
                'MerchantReturnNo' => $rma->getId(),
                'MerchantComment' => $commentText,
                'ReturnDate' => $returnDate,
                'Reason' => 'OTHER',
            ]
        );

        $createRequest->setLines($lines);

        $this->getReturnsService()->createOnChannelEngine($createRequest);
    }

    /**
     * @param Rma $rma
     * @param ReturnData $returnData
     *
     * @return void
     */
    private function updateReturn(Rma $rma, ReturnData $returnData): void
    {
        $items = $rma->getItems();
        $order = $this->orderRepository->get($rma->getOrderId());
        $lines = [];

        if (!$order) {
            return;
        }

        foreach ($items as $item) {
            $line = new Line();
            $productNo = '';

            foreach ($order->getItems() as $orderItem) {
                if ((int)$orderItem->getItemId() === (int)$item->getOrderItemId()) {
                    $productNo = $orderItem->getProductId();
                }
            }

            if (!$productNo) {
                return;
            }

            $line->setMerchantProductNo($productNo);
            $line->setRejectedQuantity((int)($item->getQtyRequested() - $item->getQtyApproved()));
            $line->setAcceptedQuantity((int)$item->getQtyApproved());
            $lines[] = $line;
        }

        $update = new MerchantReturnUpdate();
        $update->setReturnId($returnData->getReturnId());
        $update->setLines($lines);
        $this->getReturnsService()->update($update);
    }

    /**
     * @param Rma $rma
     *
     * @return ChannelEngineOrder|null
     */
    private function getOrderExtension(Rma $rma): ?ChannelEngineOrder
    {
        if (!$rma->getOrderId()) {
            return null;
        }

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('order_id', ['in' => [$rma->getOrderId()]]);

        $result = $collection->fetchItem();

        return !empty($result) ? $result : null;
    }

    /**
     * @param Rma $rma
     *
     * @return ReturnData|null
     *
     * @throws RepositoryNotRegisteredException
     * @throws QueryFilterInvalidParamException
     */
    private function getReturnData(Rma $rma): ?ReturnData
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('rmaId', Operators::EQUALS, $rma->getEntityId())
            ->where('context', Operators::EQUALS, $rma->getStoreId());

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getReturnDataRepository()->selectOne($queryFilter);
    }

    /**
     * @return ReturnsService
     */
    private function getReturnsService(): ReturnsService
    {
        return $this->returnsServiceFactory->create();
    }

    /**
     * @return RepositoryInterface
     *
     * @throws RepositoryNotRegisteredException
     */
    private function getReturnDataRepository(): RepositoryInterface
    {
        return RepositoryRegistry::getRepository(ReturnData::getClassName());
    }

    /**
     * @return OrdersConfigurationService
     */
    private function getOrderSettingsService(): OrdersConfigurationService
    {
        return ServiceRegister::getService(OrdersConfigurationService::class);
    }

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }
}
