<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Model;

use ChannelEngine\ChannelEngineIntegration\Api\RmaRepositoryFactoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Rma\Api\RmaRepositoryInterface;

/**
 * Class RmaRepositoryFactory
 *
 * @package ChannelEngine\ChannelEngineIntegration\Model
 */
class RmaRepositoryFactory implements RmaRepositoryFactoryInterface
{
    /**
     * @inheriDoc
     */
    public function create(): RmaRepositoryInterface
    {
        return ObjectManager::getInstance()->get(RmaRepositoryInterface::class);
    }
}