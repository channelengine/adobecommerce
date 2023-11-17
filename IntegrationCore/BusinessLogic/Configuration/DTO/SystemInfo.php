<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\DTO;

/**
 * Class SystemInfo
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\DTO
 */
class SystemInfo
{
    /**
     * @var string
     */
    private $systemName;
    /**
     * @var string
     */
    private $systemVersion;
    /**
     * @var string
     */
    private $shopDomain;
    /**
     * @var string
     */
    private $integrationVersion;
    /**
     * @var array
     */
    private $additionalData;

    /**
     * @param $systemName
     * @param $systemVersion
     * @param $shopDomain
     * @param $integrationVersion
     * @param array $additionalData
     */
    public function __construct(
        $systemName,
        $systemVersion,
        $shopDomain,
        $integrationVersion,
        array $additionalData = []
    ) {
        $this->systemName = $systemName;
        $this->systemVersion = $systemVersion;
        $this->shopDomain = $shopDomain;
        $this->integrationVersion = $integrationVersion;
        $this->additionalData = $additionalData;
    }

    /**
     * @return string
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @return string
     */
    public function getSystemVersion()
    {
        return $this->systemVersion;
    }

    /**
     * @return string
     */
    public function getShopDomain()
    {
        return $this->shopDomain;
    }

    /**
     * @return string
     */
    public function getIntegrationVersion()
    {
        return $this->integrationVersion;
    }

    /**
     * @return array
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }
}
