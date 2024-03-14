<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\ConfigService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\DTO\SystemInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\ServerUtility;
use ChannelEngine\ChannelEngineIntegration\Utility\UrlHelper;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class ConfigService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class ConfigService extends BaseService
{
    /**
     * UrlHelper helper class.
     *
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * Used for getting the system version.
     *
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Used for getting the integration version.
     *
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param UrlHelper $urlHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleListInterface
     */
    public function __construct(
        UrlHelper $urlHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleListInterface
    ) {
        parent::__construct();

        $this->urlHelper = $urlHelper;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleListInterface;
    }

    /**
     * Retrieves integration name.
     *
     * @return string
     */
    public function getIntegrationName(): string
    {
        return 'Magento 2';
    }

    /**
     * Returns async process starter url, always in http.
     *
     * @param string $guid Process identifier.
     *
     * @return string Formatted URL of async process starter endpoint.
     *
     * @throws NoSuchEntityException
     */
    public function getAsyncProcessUrl($guid): string
    {
        $params = [
            'guid' => $guid,
            'ajax' => 1,
            '_nosid' => true, // ignore session ID
        ];

        return $this->urlHelper->getFrontendUrl('channelengine/asyncprocess/asyncprocess', $params);
    }

    /**
     * Provides last sale price check timestamp.
     *
     * @return int
     */
    public function getLastSalePriceCheckTime(): int
    {
        return (int)$this->getConfigValue('lastSalePriceCheckTime', strtotime('today midnight'));
    }

    /**
     * Sets last sale price check time.
     *
     * @param int $time
     */
    public function setLastSalePriceCheckTime(int $time): void
    {
        $this->saveConfigValue('lastSalePriceCheckTime', (int)$time);
    }

    /**
     * @param string $state
     *
     * @return bool
     */
    public function checkInitialSyncState(string $state): bool
    {
        return $this->getInitialSyncState() === $state;
    }

    /**
     * Sets initial sync status.
     *
     * @param string $value
     *
     */
    public function setInitialSyncState(string $value): void
    {
        $this->saveConfigValue('initialSyncStatus', $value);
    }

    /**
     * Get initial sync status.
     *
     * @return string
     */
    private function getInitialSyncState(): string
    {
        return $this->getConfigValue('initialSyncStatus', 'none');
    }

    /**
     * @inheritDoc
     */
    public function getSystemInfo(): SystemInfo
    {
        return new SystemInfo(
            'magento2',
            $this->productMetadata->getEdition() . '-' . $this->productMetadata->getVersion(),
            ServerUtility::get('HTTP_HOST', 'N/A'),
            $this->getIntegrationVersion()
        );
    }

    /**
     * Returns integration version.
     *
     * @return string
     */
    private function getIntegrationVersion(): string
    {
        return $this->moduleList->getOne('ChannelEngine_ChannelEngineIntegration')['setup_version'];
    }
}
