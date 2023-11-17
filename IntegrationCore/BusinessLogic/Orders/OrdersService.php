<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Configuration\OrdersConfigurationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use DateTime;

/**
 * Class OrdersService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders
 */
abstract class OrdersService implements Contracts\OrdersService
{
    /**
     * @var Proxy
     */
    protected $proxy;
    /**
     * @var OrdersConfigurationService
     */
    protected $ordersConfigService;

    /**
     * Retrieves total number of orders for synchronization.
     *
     * @return int
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    public function getOrdersCount()
    {
        $lastSyncTime = $this->getOrdersConfigService()->getClosedOrdersSyncTime() ?: (new DateTime())->setTimestamp(0);
        $newOrdersPage = $this->getProxy()->getNew();
        $closedOrdersPage = $this->getProxy()->getWithStatuses($lastSyncTime, 1);

        return $newOrdersPage->getTotalCount() + $closedOrdersPage->getTotalCount();
    }

    /**
     * Retrieves an instance of OrdersConfigurationService.
     *
     * @return OrdersConfigurationService
     */
    protected function getOrdersConfigService()
    {
        if ($this->ordersConfigService === null) {
            $this->ordersConfigService = ServiceRegister::getService(OrdersConfigurationService::class);
        }

        return $this->ordersConfigService;
    }

    /**
     * Retrieves an instance of an orders Proxy.
     *
     * @return Proxy
     */
    protected function getProxy()
    {
        if ($this->proxy === null) {
            $this->proxy = ServiceRegister::getService(Proxy::class);
        }

        return $this->proxy;
    }
}
