<?php

namespace ChannelEngine\ChannelEngineIntegration\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySales\Model\ReturnProcessor\GetSalesChannelForOrder;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class ProcessReservationItems
 */
class ProcessReservationItems
{
    /**
     * @var SalesEventInterfaceFactory
     */
    private $salesEventFactory;

    /**
     * @var ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

    /**
     * @var PlaceReservationsForSalesEventInterface
     */
    private $placeReservationsForSalesEvent;

    /**
     * @var SalesEventExtensionFactory
     */
    private $salesEventExtensionFactory;

    /**
     * @var GetSalesChannelForOrder
     */
    private $getSalesChannelForOrder;

    /**
     * @param SalesEventInterfaceFactory $salesEventFactory
     * @param ItemToSellInterfaceFactory $itemsToSellFactory
     * @param PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param SalesEventExtensionFactory $salesEventExtensionFactory
     * @param GetSalesChannelForOrder $getSalesChannelForOrder
     */
    public function __construct(
        SalesEventInterfaceFactory              $salesEventFactory,
        ItemToSellInterfaceFactory              $itemsToSellFactory,
        PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        SalesEventExtensionFactory              $salesEventExtensionFactory,
        GetSalesChannelForOrder                 $getSalesChannelForOrder
    )
    {
        $this->salesEventFactory = $salesEventFactory;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->salesEventExtensionFactory = $salesEventExtensionFactory;
        $this->getSalesChannelForOrder = $getSalesChannelForOrder;
    }

    /**
     * Execute reservations for order items.
     *
     * @param OrderInterface $order
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order): void
    {
        $salesChannel = $this->getSalesChannelForOrder->execute($order);
        $itemToSell = [];

        foreach ($order->getItems() as $item) {
            $itemToSell[] = $this->itemsToSellFactory->create([
                'sku' => $item->getSku(),
                'qty' => -(float)$item->getQtyOrdered(),
            ]);
        }

        /** @var SalesEventExtensionInterface $salesEventExtension */
        $salesEventExtension = $this->salesEventExtensionFactory->create([
            'data' => ['objectIncrementId' => (string)$order->getIncrementId()]
        ]);

        $salesEvent = $this->salesEventFactory->create([
            'type' => SalesEventInterface::EVENT_ORDER_PLACED,
            'objectType' => SalesEventInterface::OBJECT_TYPE_ORDER,
            'objectId' => (string)$order->getEntityId()
        ]);

        $salesEvent->setExtensionAttributes($salesEventExtension);

        $this->placeReservationsForSalesEvent->execute($itemToSell, $salesChannel, $salesEvent);
    }
}