<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\DTO\ReturnsSettings;
use ChannelEngine\ChannelEngineIntegration\Entity\ReturnData;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO\ReturnResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\ReturnsService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Interfaces\RepositoryInterface;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Model\ResourceModel\ChannelEngineOrder;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationService;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Rma\Api\RmaRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class ReturnsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ReturnsService extends BaseService
{
    /**
     * @var ServiceInputProcessor
     */
    private $serviceInputProcessor;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ReturnsSettings
     */
    private $returnSettings;
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var TableFactory
     */
    protected $tableFactory;
    /**
     * @var ChannelEngineOrder
     */
    private $ceOrder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ServiceInputProcessor $serviceInputProcessor
     * @param OrderRepositoryInterface $orderRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param TableFactory $tableFactory
     * @param ChannelEngineOrder $ceOrder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ServiceInputProcessor        $serviceInputProcessor,
        OrderRepositoryInterface     $orderRepository,
        AttributeRepositoryInterface $attributeRepository,
        TableFactory                 $tableFactory,
        ChannelEngineOrder           $ceOrder,
        SearchCriteriaBuilder        $searchCriteriaBuilder
    ) {
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->orderRepository = $orderRepository;
        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->ceOrder = $ceOrder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function createInShop(ReturnResponse $returnResponse): void
    {
        try {
            if ($this->returnForOrderExists($returnResponse->getMerchantOrderNo())) {
                return;
            }

            $rma = $this->serviceInputProcessor->process(
                RmaRepositoryInterface::class,
                'save',
                $this->map($returnResponse)
            );

            $savedRma = $this->getRmaRepository()->save(reset($rma));
            $this->saveReturnData($returnResponse, $savedRma);
        } catch (CouldNotSaveException|QueryFilterInvalidParamException|Exception|
        RepositoryNotRegisteredException|BaseException|LocalizedException $e) {
            Logger::logError('Failed to save return in shop because: ' . $e->getMessage());
        }
    }

    /**
     * Checks if return for order with order number $merchantOrderNumber already exists in shop.
     *
     * @param string $merchantOrderNumber
     * @return bool
     */
    private function returnForOrderExists(string $merchantOrderNumber): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_id', $merchantOrderNumber)
            ->create();

        $returns = $this->getRmaRepository()->getList($searchCriteria)->getItems();

        return !empty($returns);
    }

    /**
     * @param ReturnResponse $returnResponse
     * @param RmaInterface $rma
     *
     * @return void
     *
     * @throws RepositoryNotRegisteredException
     */
    private function saveReturnData(ReturnResponse $returnResponse, RmaInterface $rma): void
    {
        $returnData = new ReturnData();
        $returnData->setReturnId($returnResponse->getId());
        $returnData->setContext(ConfigurationManager::getInstance()->getContext());
        $returnData->setRmaId($rma->getEntityId());

        $this->getReturnDataRepository()->save($returnData);
    }

    /**
     * @param ReturnResponse $returnResponse
     *
     * @return array[]
     *
     * @throws QueryFilterInvalidParamException
     * @throws BaseException
     * @throws LocalizedException
     */
    private function map(ReturnResponse $returnResponse): array
    {
        if (!$returnResponse->getMerchantOrderNo()) {
            throw new BaseException('Unable to import return because order id for order '
                . $returnResponse->getChannelOrderNo() . ' is not set.');
        }

        $order = $this->orderRepository->get((int)$returnResponse->getMerchantOrderNo());
        $ceOrderData = $this->ceOrder->getOrder($returnResponse->getChannelOrderNo(), $returnResponse->getMerchantOrderNo());

        if (!$order || !$ceOrderData) {
            throw new BaseException('Unable to import return because order with id '
                . $returnResponse->getMerchantOrderNo() . ' does not exist.');
        }

        return [
            'rmaDataObject' => [
                'order_id' => $returnResponse->getMerchantOrderNo(),
                'order_increment_id' => sprintf('%09d', $returnResponse->getMerchantOrderNo()),
                'store_id' => ConfigurationManager::getInstance()->getContext(),
                'customer_id' => 0,
                'date_requested' => $returnResponse->getCreatedAt()->format('Y-m-d H:i:s'),
                'customer_custom_email' => $order->getCustomerEmail(),
                'items' => $this->mapItems($returnResponse, $order),
                'status' => 'pending',
                'comments' => $returnResponse->getCustomerComment(),
            ]
        ];
    }

    /**
     * @param ReturnResponse $returnResponse
     * @param OrderInterface $order
     *
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    private function mapItems(ReturnResponse $returnResponse, OrderInterface $order): array
    {
        $items = [];
        $mappings = $this->getMappingsService()->getAttributeMappings();

        foreach ($returnResponse->getLines() as $line) {
            if ($line->getQuantity() <= 0) {
                continue;
            }

            $orderItem = null;
            foreach ($order->getItems() as $item) {
                $productId = $item->getProductId();

                if ($mappings && $mappings->getMerchantProductNumber() === AttributeMappingsService::PRODUCT_SKU) {
                    $productId = $item->getSku();
                }

                if ($productId === $line->getMerchantProductNo()) {
                    $orderItem = $item;
                }
            }

            if (!$orderItem) {
                continue;
            }

            $orderItem->setQtyShipped($line->getOrderLine()->getQuantity());
            $reason = '';

            try {
                $reasonAttribute = $this->attributeRepository->get('rma_item', 'reason');
                $sourceModel = $this->tableFactory->create();
                $sourceModel->setAttribute($reasonAttribute);

                foreach ($sourceModel->getAllOptions() as $option) {
                    if ($option['label'] === $this->getTranslationService()->translate($returnResponse->getReason())) {
                        $reason = $option['value'];
                    }
                }
            } catch (NoSuchEntityException $e) {
                Logger::logInfo('Failed to retrieve reason attribute because ' . $e->getMessage());
            }

            $items[] = [
                'order_item_id' => $orderItem->getItemId(),
                'qty_requested' => $line->getQuantity(),
                'qty_authorized' => 0,
                'qty_approved' => 0,
                'qty_returned' => 0,
                'reason' => $reason,
                'condition' => $this->getReturnSettings()->getDefaultCondition(),
                'resolution' => $this->getReturnSettings()->getDefaultResolution(),
                'status' => 'pending',
            ];
        }

        $this->orderRepository->save($order);

        return $items;
    }

    /**
     * @return ReturnsSettings
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getReturnSettings(): ReturnsSettings
    {
        if (!$this->returnSettings) {
            $this->returnSettings = $this->getReturnSettingsService()->getReturnsSettings();
        }

        return $this->returnSettings;
    }

    /**
     * @return ReturnsSettingsService
     */
    private function getReturnSettingsService(): ReturnsSettingsService
    {
        return ServiceRegister::getService(ReturnsSettingsService::class);
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
     * @return TranslationService
     */
    private function getTranslationService(): TranslationService
    {
        return ServiceRegister::getService(TranslationService::class);
    }

    /**
     * @return AttributeMappingsService
     */
    private function getMappingsService(): AttributeMappingsService
    {
        return ServiceRegister::getService(AttributeMappingsService::class);
    }

    /**
     * @return RmaRepositoryInterface
     */
    private function getRmaRepository(): RmaRepositoryInterface
    {
        return ObjectManager::getInstance()->get(RmaRepositoryInterface::class);
    }
}
