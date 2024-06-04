<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Api\AttributeValue;

/**
 * Class AttributesService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Products
 */
class AttributesService
{
    /**
     * Retrieves product attribute.
     *
     * @param AbstractAttribute[] $productAttributes
     * @param AttributeValue[] $customAttributes
     * @param string $selectedAttribute
     * @param string $selectedAttributeType
     * @param Product $product
     *
     * @return bool|string|null
     *
     */
    public function getAttribute(array $productAttributes, array $customAttributes, string $selectedAttribute, string $selectedAttributeType, Product $product)
    {
        if (!$selectedAttribute || $selectedAttribute === 'not_mapped') {
            return null;
        }

        if ($selectedAttribute === 'name') {
            $source = $productAttributes[$selectedAttribute]->getSource();

            if (!$source) {
                return null;
            }

            return $source->getAttribute()->getName();
        }

        if ($selectedAttributeType === 'boolean') {
            return $this->transformBooleanAttribute($product->getData($selectedAttribute) ?? '');
        }

        $attributeValue = $this->checkIfSelectedAttributeIsCustomAttribute(
            $productAttributes,
            $customAttributes,
            $selectedAttribute,
            $selectedAttributeType,
            $product
        );
        if ($attributeValue !== null) {
            return $attributeValue;
        }

        if ($product->getData($selectedAttribute)) {
            if ($this->checkIfDescriptionOrShortDescription($selectedAttribute)) {
                return strip_tags(htmlspecialchars_decode($product->getData($selectedAttribute)));
            }

            if ($selectedAttributeType === 'date') {
                return $this->transformDateTimeToDate($product->getData($selectedAttribute) ?? '');
            }

            if ($selectedAttributeType === 'select' || $selectedAttributeType === 'multiselect') {
                return is_array($product->getAttributeText($selectedAttribute)) ?
                    $this->transformMultiselectAttribute($product->getAttributeText($selectedAttribute)) :
                    $product->getAttributeText($selectedAttribute);
            }

            return $product->getData($selectedAttribute);
        }

        return null;
    }

    /**
     * Checks if selected attribute is custom product attribute
     *
     * @param array $productAttributes
     * @param array $customAttributes
     * @param string $selectedAttribute
     * @param string $selectedAttributeType
     * @param Product $product
     * @return bool|string|null
     */
    private function checkIfSelectedAttributeIsCustomAttribute(array $productAttributes, array $customAttributes, string $selectedAttribute, string $selectedAttributeType, Product $product)
    {
        foreach ($customAttributes as $attribute) {
            if (isset($productAttributes[$selectedAttribute]) && $attribute->getAttributeCode() === $selectedAttribute
                && $productAttributes[$selectedAttribute]->isScopeGlobal()) {
                $source = $productAttributes[$selectedAttribute]->getSource();

                if (!$source) {
                    return null;
                }

                if (is_array($attribute->getValue())) {
                    $result = '';

                    foreach ($attribute->getValue() as $value) {
                        $result .= $source->getOptionText($value) . ' ';
                    }

                    return $result;
                }

                if ($selectedAttributeType === 'boolean') {
                    return $this->transformBooleanAttribute($attribute->getValue() ?? '');
                }

                if ($this->checkIfDescriptionOrShortDescription($selectedAttribute)) {
                    return strip_tags(htmlspecialchars_decode(
                        $source->getOptionText($attribute->getValue()) ?: $attribute->getValue()
                    ));
                }

                if ($selectedAttributeType === 'date') {
                    return $this->transformDateTimeToDate($attribute->getValue() ?: '');
                }

                if ($selectedAttributeType === 'select' || $selectedAttributeType === 'multiselect') {
                    return is_array($product->getAttributeText($selectedAttribute)) ?
                        $this->transformMultiselectAttribute($product->getAttributeText($selectedAttribute)) :
                        $product->getAttributeText($selectedAttribute);
                }

                return $source->getOptionText($attribute->getValue()) ?: $attribute->getValue();
            }
        }

        return null;
    }

    /**
     * Checks if selected attribute is description or short description
     *
     * @param string $selectedAttribute
     * @return bool
     */
    private function checkIfDescriptionOrShortDescription(string $selectedAttribute): bool
    {
        return $selectedAttribute === 'description' || $selectedAttribute === 'short_description';
    }

    /**
     * Transforms multiselect attribute.
     *
     * @param array $data
     * @return string
     */
    private function transformMultiselectAttribute(array $data): string
    {
        return implode(',', $data);
    }

    /**
     * Transforms boolean attribute.
     *
     * @param string $attribute
     * @return string
     */
    private function transformBooleanAttribute(string $attribute): string
    {
        return $attribute === '1' ? 'Yes' : 'No';
    }

    /**
     * Transforms datetime to date.
     *
     * @param string $dateTime
     * @return string
     */
    private function transformDateTimeToDate(string $dateTime): string
    {
        return date("Y-m-d", strtotime($dateTime));
    }
}
