<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\DTO\AuthInfo;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Exceptions\FailedToRetrieveAuthInfoException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Interface AuthorizationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\Contracts
 */
interface AuthorizationService
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;

    /**
     * Retrieves auth info object for the current user
     *
     * @return AuthInfo Instance of auth info object.
     *
     * @throws QueryFilterInvalidParamException
     * @throws FailedToRetrieveAuthInfoException
     */
    public function getAuthInfo();

    /**
     * Sets auth info for the current user.
     *
     * @param AuthInfo $authInfo Auth info object instance.
     */
    public function setAuthInfo($authInfo = null);
}