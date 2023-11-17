<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts;

/**
 * Class ReturnReason
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Returns\Contracts
 */
interface ReturnReason
{
    const PRODUCT_DEFECT = 'PRODUCT_DEFECT';
    const PRODUCT_UNSATISFACTORY = 'PRODUCT_UNSATISFACTORY';
    const WRONG_PRODUCT = 'WRONG_PRODUCT';
    const TOO_MANY_PRODUCTS = 'TOO_MANY_PRODUCTS';
    const REFUSED = 'REFUSED';
    const REFUSED_DAMAGED = 'REFUSED_DAMAGED';
    const WRONG_ADDRESS = 'WRONG_ADDRESS';
    const NOT_COLLECTED = 'NOT_COLLECTED';
    const WRONG_SIZE = 'WRONG_SIZE';
    const OTHER = 'OTHER';
}