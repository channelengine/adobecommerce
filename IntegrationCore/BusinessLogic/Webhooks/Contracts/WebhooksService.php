<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts;

/**
 * Interface WebhooksService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Webhooks\Contracts
 */
interface WebhooksService
{
    /**
     * Creates webhook.
     *
     * @return void
     */
    public function create();

    /**
     * Deletes webhook.
     *
     * @return void
     */
    public function delete();

    /**
     * Creates webhook token.
     *
     * @return void
     */
    public function createWebhookToken();

    /**
     * Retrieves webhook token.
     *
     * @return string
     */
    public function getWebhookToken();
}