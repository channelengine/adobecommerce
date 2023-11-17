<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts;

/**
 * Interface Context
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts
 */
interface Context
{
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';
    const SUCCESS = 'success';
    const NONE = 'none';
}