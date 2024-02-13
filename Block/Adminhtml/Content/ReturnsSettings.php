<?php

namespace ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\ReturnsSettingsService;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Rma\Api\RmaAttributesManagementInterface;

/**
 * Class ReturnsSettings
 *
 * @package ChannelEngine\ChannelEngineIntegration\Block\Adminhtml\Content
 */
class ReturnsSettings extends Template
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        Context           $context,
        array             $data = []
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves list of item conditions.
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getItemConditions(): array
    {
        return $this->getOptions('condition');
    }

    /**
     * Retrieves return settings.
     *
     * @return \ChannelEngine\ChannelEngineIntegration\DTO\ReturnsSettings|null
     */
    public function getReturnsSettings(): ?\ChannelEngine\ChannelEngineIntegration\DTO\ReturnsSettings
    {
        try {
            return $this->getReturnsSettingsService()->getReturnsSettings();
        } catch (QueryFilterInvalidParamException $e) {
            return null;
        }
    }

    /**
     * Retrieves list of item resolutions.
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getItemResolution(): array
    {
        return $this->getOptions('resolution');
    }

    /**
     * @param string $code
     *
     * @return array
     *
     * @throws LocalizedException
     */
    private function getOptions(string $code): array
    {
        $attribute = $this->eavConfig->getAttribute(RmaAttributesManagementInterface::ENTITY_TYPE, $code);

        if (!$attribute) {
            return [];
        }

        $options = $attribute->getSource()->getAllOptions();

        foreach ($options as $key => $option) {
            if (empty($option['value'])) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * @return ReturnsSettingsService
     */
    private function getReturnsSettingsService(): ReturnsSettingsService
    {
        return ServiceRegister::getService(ReturnsSettingsService::class);
    }
}
