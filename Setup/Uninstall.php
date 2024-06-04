<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Setup;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts\ReturnReason;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts\WebhooksService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Class Uninstall
 *
 * @package ChannelEngine\ChannelEngineIntegration\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    private $attributeValues;
    /**
     * @var TableFactory
     */
    private $tableFactory;
    /**
     * @var AttributeOptionManagementInterface
     */
    private $attributeOptionManagement;
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param TableFactory $tableFactory
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        TableFactory                       $tableFactory,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeRepositoryInterface       $attributeRepository,
        ProductMetadataInterface           $productMetadata
    ) {
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeRepository = $attributeRepository;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $this->deleteWebhooks();
        $this->dropTable('channel_engine_entity', $setup);
        $this->dropTable('channel_engine_queue', $setup);
        $this->dropTable('channel_engine_events', $setup);
        $this->dropTable('channel_engine_logs', $setup);
        $this->dropTable('channel_engine_order', $setup);

        if ($this->productMetadata->getEdition() === 'Enterprise') {
            $this->deleteReasons();
            $this->dropTable('channel_engine_returns', $setup);
        }
    }

    /**
     * @param string $tableName
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    private function dropTable(string $tableName, SchemaSetupInterface $setup): void
    {
        if ($setup->tableExists($tableName)) {
            $setup->getConnection()->dropTable($tableName);
        }
    }

    private function deleteWebhooks(): void
    {
        /** @var WebhooksService $webhookService */
        $webhookService = ServiceRegister::getService(WebhooksService::class);
        $webhookService->delete();
    }

    /**
     * @return void
     *
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function deleteReasons(): void
    {
        $ceReasons = [
            ReturnReason::PRODUCT_DEFECT,
            ReturnReason::NOT_COLLECTED,
            ReturnReason::PRODUCT_UNSATISFACTORY,
            ReturnReason::REFUSED,
            ReturnReason::REFUSED_DAMAGED,
            ReturnReason::TOO_MANY_PRODUCTS,
            ReturnReason::WRONG_ADDRESS,
            ReturnReason::WRONG_PRODUCT,
            ReturnReason::WRONG_SIZE,
            ReturnReason::OTHER
        ];

        foreach ($ceReasons as $reason) {
            $this->deleteOption('reason', __($reason)->getText());
        }
    }

    /**
     * @param string $attributeCode
     * @param string $label
     *
     * @return void
     *
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function deleteOption(string $attributeCode, string $label): void
    {
        $optionId = $this->getOptionId($attributeCode, $label);

        if ($optionId) {
            return;
        }

        $this->attributeOptionManagement->delete('rma_item', $attributeCode, $optionId);
    }

    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     *
     * @return int|false
     *
     * @throws NoSuchEntityException
     */
    private function getOptionId(string $attributeCode, string $label)
    {
        $attribute = $this->getAttribute($attributeCode);

        if (!isset($this->attributeValues[$attribute->getAttributeId()])) {
            $this->attributeValues[$attribute->getAttributeId()] = [];

            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[$attribute->getAttributeId()][$option['label']] = $option['value'];
            }
        }

        return $this->attributeValues[$attribute->getAttributeId()][$label] ?? false;
    }

    /**
     * @param $attributeCode
     *
     * @return AttributeInterface
     *
     * @throws NoSuchEntityException
     */
    private function getAttribute($attributeCode): AttributeInterface
    {
        return $this->attributeRepository->get('rma_item', $attributeCode);
    }
}
