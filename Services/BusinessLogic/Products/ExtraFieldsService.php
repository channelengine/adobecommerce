<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use ChannelEngine\ChannelEngineIntegration\DTO\ExtraDataAttributeMappings;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\CustomAttribute;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ExtraDataAttributeMappingsService;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class ExtraFieldsService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class ExtraFieldsService
{
    public const CATEGORY_ATTRIBUTE = 'category_ids';
    /**
     * @var ExtraDataAttributeMappings
     */
    private $extraDataMappings;
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Retrieves product extra fields.
     *
     * @param AbstractAttribute[] $productAttributes
     * @param AttributeValue[] $customAttributes
     * @param array $productData
     *
     * @return array
     *
     * @throws LocalizedException
     * @throws QueryFilterInvalidParamException
     */
    public function getExtraFields(array $productAttributes, array $customAttributes, array $productData): array
    {
        $extraFields = [];

        foreach ($productData as $key => $item) {
            if (!$this->getMapping($key)) {
                continue;
            }

            if ($key === self::CATEGORY_ATTRIBUTE) {
                $extraFields[] = new CustomAttribute(
                    $this->getMapping($key),
                    $this->categoryService->getCategoryTrail($item),
                    CustomAttribute::TYPE_TEXT
                );

                continue;
            }

            $value = $item;

            foreach ($customAttributes as $attribute) {
                if ($attribute->getAttributeCode() === $key) {
                    if (!isset($productAttributes[$attribute->getAttributeCode()])) {
                        continue;
                    }

                    $source = $productAttributes[$attribute->getAttributeCode()]->getSource();

                    if (is_array($attribute->getValue())) {
                        foreach ($attribute->getValue() as $attributeValue) {
                            $sourceValue = $source->getOptionText($attributeValue);
                            $value .= $sourceValue ?: $attributeValue;
                        }
                    } else {
                        $sourceValue = $source->getOptionText($attribute->getValue());
                        $value = (isset($sourceValue) && $sourceValue) ? $sourceValue : $attribute->getValue();
                    }
                }
            }

            $fieldValue = $value;

            if (is_array($value)) {
                $fieldValue = implode(' ', $value);
            }

            if ($value instanceof Phrase) {
                $fieldValue = $value->getText();
            }


            $extraFields[] = new CustomAttribute(
                $this->getMapping($key),
                $fieldValue,
                CustomAttribute::TYPE_TEXT
            );

        }

        return $extraFields;
    }

    /**
     * @param string $attributeKey
     * @return string
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getMapping(string $attributeKey): string
    {
        $extraDataMappings = $this->getExtraDataMappings();

        if (!$extraDataMappings) {
            return '';
        }

        foreach ($extraDataMappings->getMappings() as $key => $value) {
            if ($value === $attributeKey) {
                return $key;
            }
        }

        return '';
    }

    /**
     * @return ExtraDataAttributeMappings|null
     *
     * @throws QueryFilterInvalidParamException
     */
    private function getExtraDataMappings(): ?ExtraDataAttributeMappings
    {
        if (!$this->extraDataMappings) {
            $this->extraDataMappings = $this->getExtraDataMappingsService()->getExtraDataAttributeMappings();
        }

        return $this->extraDataMappings;
    }

    /**
     * @return ExtraDataAttributeMappingsService
     */
    private function getExtraDataMappingsService(): ExtraDataAttributeMappingsService
    {
        return ServiceRegister::getService(ExtraDataAttributeMappingsService::class);
    }
}
