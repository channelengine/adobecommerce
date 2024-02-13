<?php

namespace ChannelEngine\ChannelEngineIntegration\Setup\Patch\Data;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts\ReturnReason;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts\TranslationService;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\OptionLabel;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class Initializer
 *
 * @package ChannelEngine\ChannelEngineIntegration\Setup\Patch\Data
 */
class Initializer implements DataPatchInterface
{
    /**
     * @var array
     */
    protected $attributeValues;
    /**
     * @var TableFactory
     */
    protected $tableFactory;
    /**
     * @var AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;
    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;
    /**
     * @var AttributeOptionInterfaceFactory
     */
    protected $optionFactory;
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var TranslationService
     */
    private $translationService;

    /**
     * Data constructor.
     *
     * @param TableFactory $tableFactory
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param AttributeOptionInterfaceFactory $optionFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param \ChannelEngine\ChannelEngineIntegration\Utility\Initializer $initializer
     *
     * @throws RepositoryClassException
     */
    public function __construct(
        TableFactory                         $tableFactory,
        AttributeOptionManagementInterface   $attributeOptionManagement,
        AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        AttributeOptionInterfaceFactory      $optionFactory,
        AttributeRepositoryInterface         $attributeRepository,
        \ChannelEngine\ChannelEngineIntegration\Utility\Initializer $initializer
    ) {
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->attributeRepository = $attributeRepository;
        $initializer->init();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     *
     * @throws LocalizedException
     */
    public function apply(): void
    {
        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = ObjectManager::getInstance()->get(ProductMetadataInterface::class);
        $edition = $productMetadata->getEdition();
        if ($edition === 'Community') {
            return;
        }

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
            $this->addOption('reason', $this->getTranslationService()->translate($reason));
        }
    }

    /**
     * Find or create a matching attribute option
     *
     * @param string $attributeCode Attribute the option should exist in
     * @param string $label Label to find or add
     *
     * @return void
     *
     * @throws LocalizedException
     */
    private function addOption(string $attributeCode, string $label): void
    {
        $optionId = $this->getOptionId($attributeCode, $label);

        if ($optionId) {
            return;
        }

        /** @var OptionLabel $optionLabel */
        $optionLabel = $this->optionLabelFactory->create();
        $optionLabel->setStoreId(0);
        $optionLabel->setLabel($label);

        $option = $this->optionFactory->create();
        $option->setLabel($optionLabel->getLabel());
        $option->setStoreLabels([$optionLabel]);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        $this->attributeOptionManagement->add(
            'rma_item',
            $attributeCode,
            $option
        );
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

            /** @var Table $sourceModel */
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

    /**
     * @return TranslationService
     */
    private function getTranslationService(): TranslationService
    {
        if ($this->translationService === null) {
            $this->translationService = ServiceRegister::getService(TranslationService::class);
        }

        return $this->translationService;
    }
}
