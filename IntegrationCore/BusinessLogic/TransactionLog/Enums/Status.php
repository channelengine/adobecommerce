<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Enums;

/**
 * Interface Status
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Enums
 */
interface Status
{
    const QUEUED = 'queued';
    const IN_PROGRESS = 'in_progress';
    const COMPLETED = 'completed';
    const PARTIALLY_COMPLETED = 'partially_completed';
    const FAILED = 'failed';
    const ABORTED = 'aborted';
}