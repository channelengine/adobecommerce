<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlInterface;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlTransport;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Helper\Helper;
use Magento\FunctionalTestingFramework\Util\MftfGlobals;

/**
 * Class WebhookExecutor
 *
 * @package  ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper
 */
class CeWebhook extends Helper
{

    /**
     * Executes webhook
     * @param string $url
     * @param string $httpMethod
     * @param string|null $dataAsJson
     * @return void
     * @throws TestFrameworkException
     * @throws QueryFilterInvalidParamException
     */
    public function executeWebHook(string $url ='',string $httpMethod = 'GET', string $dataAsJson = null)
    {
        $data = [];
        if ($dataAsJson) {
            $data = json_decode($dataAsJson);
        }
        $urlBase = MftfGlobals::getBaseUrl();
        $url =  $urlBase.$url;
        try {
            $transport = new CurlTransport();
            if ($data && ($httpMethod === CurlInterface::GET)) {
                $url =  $url.'&'.http_build_query($data);
            }
            $transport->write($url, $data, $httpMethod);
            $transport->read();
            $transport->close();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}