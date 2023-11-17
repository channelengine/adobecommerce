<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class Webhook
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\DTO
 */
class Webhook extends DataTransferObject
{
    /**
     * @var string
     */
    protected $tenant;
    /**
     * @var string
     */
    protected $token;
    /**
     * @var string
     */
    protected $event;

    /**
     * Webhook constructor.
     *
     * @param string $tenant
     * @param string $token
     * @param string $event
     */
    public function __construct($tenant, $token, $event)
    {
        $this->tenant = $tenant;
        $this->token = $token;
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * @param string $tenant
     */
    public function setTenant($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'tenant' => $this->getTenant(),
            'token' => $this->getToken(),
            'event' => $this->getEvent(),
        ];
    }
}