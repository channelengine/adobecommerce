<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use ChannelEngine\ChannelEngineIntegration\Exceptions\CancellationRejectedException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Contracts\CancellationService as BaseService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationItem;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\CancellationRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain\RejectResponse;
use Exception;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class CancellationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class CancellationService implements BaseService
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public function getAllItems($orderId): array
    {
        $order = $this->orderRepository->get($orderId);
        $items = [];

        foreach ($order->getItems() as $orderItem) {
            $items[] = new CancellationItem(
                $orderItem->getProductId(),
                (int)$orderItem->getQtyOrdered(),
                true
            );
        }

        return $items;
    }

    /**
     * @inheritDoc
     *
     * @throws CancellationRejectedException
     */
    public function reject(CancellationRequest $request, Exception $reason): RejectResponse
    {
        $error = json_decode($reason->getMessage(), true);
        throw new CancellationRejectedException(__('Order cancellation failed. Reason: ') .
            ($error['message'] ?? ''));
    }
}
