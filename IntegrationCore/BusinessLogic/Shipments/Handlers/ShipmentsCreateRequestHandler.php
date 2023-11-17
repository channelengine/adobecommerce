<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO\MerchantShipmentLineRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO\MerchantShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\Context;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\Exceptions\FailedToRetrieveOrdersChannelSupportEntityException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\OrdersChannelSupportCache;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Contracts\ShipmentsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\CreateShipmentRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Domain\OrderItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;

/**
 * Class ShipmentsCreateRequestHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Shipments\Handlers
 */
class ShipmentsCreateRequestHandler
{
    /**
     * @var ShipmentsService
     */
    protected $shipmentsService;
    /**
     * @var OrdersChannelSupportCache
     */
    protected $supportCache;
    /**
     * @var Proxy
     */
    protected $shipmentsProxy;

    /**
     * Handles shipments create request.
     *
     * @param CreateShipmentRequest $request
     *
     * @throws QueryFilterInvalidParamException
     * @throws FailedToRetrieveOrdersChannelSupportEntityException
     * @throws RepositoryNotRegisteredException
     */
    public function handle(CreateShipmentRequest $request)
    {
        if ($this->mustWaitForCompleteShipment($request)) {
            $items = $this->getShipmentsService()->getAllItems($request->getShopOrderId());
            foreach ($items as $item) {
                if (!$item->isShipped()) {
                    // We must wait until all items are shipped.
                    return;
                }
            }

            $request = $this->getCompleteCreateShipmentRequest($request, $items);
        }

        try {
            $this->getShipmentsProxy()->createShipment($this->prepareDto($request));
        } catch (BaseException $e) {
            $error = json_decode($e->getMessage(), true);
            $response = $this->getShipmentsService()->rejectCreate($request, $e);

            if (!$response->isPermitted()) {
                $notification = new Notification();
                $notification->setNotificationContext(Context::ERROR);
                $notification->setCreatedAt(new DateTime());
                $notification->setMessage('Shipment upload finished with errors: %s');
                $notification->setArguments(isset($error['message']) ? $error['message'] : '');
                $notification->setContext(ConfigurationManager::getInstance()->getContext());

                $this->getNotificationService()->create($notification);
            }
        }
    }

    /**
     * @param CreateShipmentRequest $request
     * @param OrderItem[] $items
     *
     * @return CreateShipmentRequest
     */
    protected function getCompleteCreateShipmentRequest(CreateShipmentRequest $request, array $items)
    {
        return new CreateShipmentRequest(
            $request->getShopOrderId(),
            $items,
            $request->isPartial(),
            $request->getMerchantShipmentNo(),
            $request->getMerchantOrderNo(),
            $request->getTrackTraceNo(),
            $request->getTrackTraceUrl(),
            $request->getReturnTrackTraceNo(),
            $request->getMethod()
        );
    }

    /**
     * @param CreateShipmentRequest $request
     *
     * @return MerchantShipmentRequest
     */
    protected function prepareDto(CreateShipmentRequest $request)
    {
        $dto = new MerchantShipmentRequest();

        $dto->setMethod($request->getMethod());
        $dto->setReturnTrackTraceNo($request->getReturnTrackTraceNo());
        $dto->setTrackTraceUrl($request->getTrackTraceUrl());
        $dto->setTrackTraceNo($request->getTrackTraceNo());
        $dto->setMerchantOrderNo($request->getMerchantOrderNo());
        $dto->setMerchantShipmentNo($request->getMerchantShipmentNo());
        $dto->setLines(array_map(static function (OrderItem $item) {
            $lineItem = new MerchantShipmentLineRequest();
            $lineItem->setQuantity($item->getQuantity());
            $lineItem->setMerchantProductNo($item->getMerchantProductNo());

            return $lineItem;
        }, $request->getOrderItems()));

        return $dto;
    }

    /**
     * Checks whether all items must be shipped before submitting request.
     *
     * @param CreateShipmentRequest $request
     *
     * @return bool
     *
     * @throws FailedToRetrieveOrdersChannelSupportEntityException
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     */
    private function mustWaitForCompleteShipment(CreateShipmentRequest $request)
    {
        return $request->isPartial()
            && !$this->getSupportCache()->isPartialShipmentSupported($request->getShopOrderId());
    }

    /**
     * Retrieves an instance of ShipmentsService.
     *
     * @return ShipmentsService
     */
    protected function getShipmentsService()
    {
        if ($this->shipmentsService === null) {
            $this->shipmentsService = ServiceRegister::getService(ShipmentsService::class);
        }

        return $this->shipmentsService;
    }

    /**
     * Retrieves an instance of OrdersChannelSupportCache.
     *
     * @return OrdersChannelSupportCache
     */
    protected function getSupportCache()
    {
        if ($this->supportCache === null) {
            $this->supportCache = ServiceRegister::getService(OrdersChannelSupportCache::class);
        }

        return $this->supportCache;
    }

    /**
     * Retrieves an instance of shipments Proxy.
     *
     * @return Proxy
     */
    protected function getShipmentsProxy()
    {
        if ($this->shipmentsProxy === null) {
            $this->shipmentsProxy = ServiceRegister::getService(Proxy::class);
        }

        return $this->shipmentsProxy;
    }

    /**
     * @return TimeProvider
     */
    protected function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }

    /**
     * @return NotificationService
     */
    protected function getNotificationService()
    {
        return ServiceRegister::getService(NotificationService::class);
    }
}