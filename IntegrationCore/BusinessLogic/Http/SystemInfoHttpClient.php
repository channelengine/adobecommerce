<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Http;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\ConfigService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Configuration\DTO\SystemInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\HttpClient;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\ServerUtility;

/**
 * Class SystemInfoHttpClient
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Http
 */
class SystemInfoHttpClient extends HttpClient
{
    /** @var HttpClient */
    private $httpClient;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Adds the 'User-Agent' header containing information about the system that's sending the request.
     *
     * @inheritDoc
     */
    public function request($method, $url, $headers = array(), $body = '')
    {
        $headers['User-Agent'] = $this->generateUserAgentHeader($headers);

        return $this->httpClient->request($method, $url, $headers, $body);
    }

    /**
     * Adds the 'User-Agent' header containing information about the system that's sending the request.
     *
     * @inheritDoc
     */
    public function requestAsync($method, $url, $headers = array(), $body = '1')
    {
        $headers['User-Agent'] = $this->generateUserAgentHeader($headers);

        $this->httpClient->requestAsync($method, $url, $headers, $body);
    }

    /**
     * @inheritDoc
     */
    protected function sendHttpRequest($method, $url, $headers = array(), $body = '')
    {
        return $this->httpClient->sendHttpRequest($method, $url, $headers, $body);
    }

    /**
     * @inheritDoc
     */
    protected function sendHttpRequestAsync($method, $url, $headers = array(), $body = '1')
    {
        return $this->httpClient->sendHttpRequestAsync($method, $url, $headers, $body);
    }

    /**
     * Generates the 'User-Agent' header.
     *
     * @param $headers - Previously set request headers
     *
     * @return string
     */
    private function generateUserAgentHeader($headers)
    {
        /** @var ConfigService $configService */
        $configService = $this->getConfigService();
        $systemInfo = $configService->getSystemInfo();

        $userAgentHeader = $this->generateUserAgentValue($systemInfo);

        foreach ($systemInfo->getAdditionalData() as $additionalDatumKey => $additionalDatumValue) {
            $userAgentHeader .= ' ' . $this->formatUserAgentData($additionalDatumKey) . '/' .
                $this->formatUserAgentData($additionalDatumValue);
        }

        return !isset($headers['User-Agent']) ?
            $userAgentHeader :
            $headers['User-Agent'] . ' ' . $userAgentHeader;
    }

    /**
     * Generates the user agent value string out of given system info object.
     *
     * @param SystemInfo $systemInfo
     *
     * @return string
     */
    public function generateUserAgentValue(SystemInfo $systemInfo)
    {
        return 'SystemName/' . $this->formatUserAgentData($systemInfo->getSystemName()) .
            ' SystemVersion/' . $this->formatUserAgentData($systemInfo->getSystemVersion()) .
            ' IntegrationVersion/' . $this->formatUserAgentData($systemInfo->getIntegrationVersion()) .
            ' Php/' . $this->formatUserAgentData(PHP_VERSION) .
            ' Server/' . $this->formatUserAgentData(ServerUtility::get('SERVER_SOFTWARE', 'N/A')) .
            ' Domain/' . $this->formatUserAgentData($systemInfo->getShopDomain());
    }

    /**
     * Trims unnecessary surrounding whitespace and replaces new lines and tabs with spaces in the given user agent
     * data segment.
     *
     * @param $userAgentSegment
     *
     * @return string
     */
    private function formatUserAgentData($userAgentSegment)
    {
        return preg_replace('/\s+/', ' ', trim($userAgentSegment));
    }
}
