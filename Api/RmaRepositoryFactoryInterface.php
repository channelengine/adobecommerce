<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Api;

use Magento\Rma\Api\RmaRepositoryInterface;

/**
 * Interface RmaRepositoryFactoryInterface
 *
 * @package ChannelEngine\ChannelEngineIntegration\Api
 */
interface RmaRepositoryFactoryInterface
{
    /**
     * Returns an instance of RmaRepositoryInterface.
     *
     * @return RmaRepositoryInterface
     */
    public function create(): RmaRepositoryInterface;
}