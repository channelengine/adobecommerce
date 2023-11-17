<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Cancellation\Domain;

class CancellationItem
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var int
     */
    protected $quantity;
    /**
     * @var bool
     */
    protected $cancelled;

    /**
     * CancellationItem constructor.
     *
     * @param string $id
     * @param int $quantity
     * @param bool $cancelled
     */
    public function __construct($id, $quantity, $cancelled)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->cancelled = $cancelled;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

}