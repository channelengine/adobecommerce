<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility;

/**
 * Class ServerUtility.
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility
 */
class ServerUtility
{
    /**
     * Gets value from $_SERVER variable if set under the given key, otherwise returns the given default value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }
}
