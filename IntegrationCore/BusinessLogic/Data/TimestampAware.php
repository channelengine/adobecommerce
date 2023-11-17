<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Data;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use DateTime;
use Exception;

/**
 * Class TimestampAware
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Data
 */
abstract class TimestampAware extends DataTransferObject
{
    /**
     * Retrieves DateTime from string.
     *
     * @param string | int | null $timestamp
     *
     * @return DateTime| null
     */
    protected static function getDate($timestamp)
    {
        if ($timestamp === null) {
            return null;
        }

        $dateTime = null;

        try {
            $dateTime = self::instantiateDate($timestamp);
        } catch (Exception $e) {
            Logger::logWarning("Failed to instantiate date time. Provided date [$timestamp].");
        }

        return $dateTime;
    }

    /**
     * Retrieves timestamp from DateTime.
     *
     * @param DateTime | string  | null $dateTime
     *
     * @return string
     */
    protected static function getTimestamp($dateTime)
    {
        if ($dateTime === null) {
            return null;
        }

        if (is_string($dateTime)) {
            return $dateTime;
        }

        return (string)$dateTime->getTimestamp();
    }

    /**
     * Creates date time instance from a timestamp.
     *
     * @param int | string $timestamp Time stamp.
     *
     * @return DateTime Date time instance.
     *
     * @throws Exception Thrown when the date time instance cannot be created.
     */
    protected static function instantiateDate($timestamp)
    {
        if (is_numeric($timestamp)) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($timestamp);
        } else {
            $dateTime = new DateTime($timestamp);
        }

        return $dateTime;
    }
}
