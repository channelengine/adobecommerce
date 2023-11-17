<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Handlers;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\DTO\Cancellation;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\DTO\Line;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Cancellation\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO\Shipment;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Contracts\CancellationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\Context;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\NotificationService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Entities\Notification;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\ChannelSupport\OrdersChannelSupportCache;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;
use Exception;

/**
 * Class CancellationRequestHandler
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Handlers
 */
class CancellationRequestHandler
{
    public function handle(CancellationRequest $request, $merchantOrderNo)
    {
        if ($this->mustWaitForCompleteCancellation($request)) {
            $items = $this->getCancellationService()->getAllItems($request->getOrderId());
            foreach ($items as $item) {
                if (!$item->isCancelled()) {
                    // We must wait until all items are cancelled.
                    return;
                }
            }

            $request = $this->getCompleteCancellationRequest($request, $items);
        }

        try {
            $shipments = $this->getShipmentsProxy()->getShipmentByMerchantOrderNo($merchantOrderNo);
            $this->getProxy()->submit($this->prepareDto($request, $shipments));
        } catch (Exception $e) {
            $error = json_decode($e->getMessage(), true);
            $response = $this->getCancellationService()->reject($request, $e);

            if (!$response->isPermitted()) {
                $notification = new Notification();
                $notification->setNotificationContext(Context::ERROR);
                $notification->setCreatedAt(new DateTime());
                $notification->setMessage('Order cancellation finished with errors: %s');
                $notification->setArguments(isset($error['message']) ? $error['message'] : '');
                $notification->setContext(ConfigurationManager::getInstance()->getContext());

                $this->getNotificationService()->create($notification);
            }
        }
    }

    private function getCompleteCancellationRequest(CancellationRequest $request, array $items)
    {
        return new CancellationRequest(
            $request->getId(),
            $request->getOrderId(),
            $items,
            false,
            $request->getReasonCode(),
            $request->getReason()
        );
    }

    /**
     * Prepares cancellation dto.
     *
     * @param CancellationRequest $request
     * @param Shipment[] $shipments
     *
     * @return Cancellation
     */
    private function prepareDto(CancellationRequest $request, array $shipments = null)
    {
        $dto = new Cancellation();
        $itemQuantities = [];

        foreach ($shipments as $shipment) {
            foreach ($shipment->getLines() as $line) {
                if (!isset($itemQuantities[$line->getMerchantProductNo()])) {
                    $itemQuantities[$line->getMerchantProductNo()] = [
                        'shippedQuantity' => $line->getQuantity(),
                        'quantity' => $line->getOrderLine()->getQuantity(),
                    ];
                } else {
                    $itemQuantities[$line->getMerchantProductNo()]['shippedQuantity'] += $line->getQuantity();
                }
            }
        }

        $dto->setLines(array_map(static function (CancellationItem $item) use ($itemQuantities) {
            $lineItem = new Line();
            $lineItem->setQuantity($item->getQuantity());

            if (isset($itemQuantities[$item->getId()]) &&
                ($itemQuantities[$item->getId()]['quantity'] - $itemQuantities[$item->getId()]['shippedQuantity'])
                < $item->getQuantity()) {
                $lineItem->setQuantity(
                    $itemQuantities[$item->getId()]['quantity'] -
                    $itemQuantities[$item->getId()]['shippedQuantity']
                );
            }

            $lineItem->setMerchantProductNo($item->getId());

            return $lineItem;
        }, $request->getLineItems()));
        $dto->setReasonCode($request->getReasonCode());
        $dto->setReason($request->getReason());
        $dto->setMerchantOrderNo($request->getOrderId());
        $dto->setMerchantCancellationNo($request->getId());

        return $dto;
    }

    /**
     * Provides channel support cache service.
     *
     * @return OrdersChannelSupportCache
     */
    private function getChannelSupportCacheService()
    {
        return ServiceRegister::getService(OrdersChannelSupportCache::CLASS_NAME);
    }

    /**
     * @return CancellationService
     */
    private function getCancellationService()
    {
        return ServiceRegister::getService(CancellationService::class);
    }

    /**
     * @return Proxy
     */
    private function getProxy()
    {
        return ServiceRegister::getService(Proxy::class);
    }

    /**
     * Checks whether all items must be canceled before submitting request.
     *
     * @param CancellationRequest $request
     *
     * @return bool
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function mustWaitForCompleteCancellation(CancellationRequest $request)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $request->isPartialCancellation()
            && !$this->getChannelSupportCacheService()->isPartialCancellationSupported($request->getOrderId());
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

    /**
     * @return \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\Http\Proxy
     */
    protected function getShipmentsProxy()
    {
        return ServiceRegister::getService(\ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\Http\Proxy::class);
    }
}