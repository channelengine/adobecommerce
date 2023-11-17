<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain;

class CancellationRequest
{
    const REASON_NOT_IN_STOCK = 'NOT_IN_STOCK';
    const REASON_DAMAGED = 'DAMAGED';
    const REASON_INCOMPLETE = 'INCOMPLETE';
    const REASON_CLIENT_CANCELLED = 'CLIENT_CANCELLED';
    const REASON_INVALID_ADDRESS = 'INVALID_ADDRESS';
    const REASON_OTHER = 'OTHER';


    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $orderId;
    /**
     * @var CancellationItem[]
     */
    protected $lineItems;
    /**
     * @var bool
     */
    protected $isPartialCancellation;
    /**
     * @var string
     */
    protected $reasonCode;
    /**
     * @var string | null
     */
    protected $reason;

    /**
     * CancellationRequest constructor.
     * @param string $id
     * @param string $orderId
     * @param CancellationItem[] $lineItems
     * @param bool $isPartialCancellation
     * @param string $reasonCode
     * @param string|null $reason
     */
    public function __construct($id, $orderId, array $lineItems, $isPartialCancellation, $reasonCode, $reason = null)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->lineItems = $lineItems;
        $this->isPartialCancellation = $isPartialCancellation;
        $this->reasonCode = $reasonCode;
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return CancellationItem[]
     */
    public function getLineItems()
    {
        return $this->lineItems;
    }

    /**
     * @return bool
     */
    public function isPartialCancellation()
    {
        return $this->isPartialCancellation;
    }

    /**
     * @return string
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }
}