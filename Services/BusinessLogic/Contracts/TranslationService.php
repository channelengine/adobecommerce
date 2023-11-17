<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts;

/**
 * Class TranslationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic\Contracts
 */
interface TranslationService
{
    /**
     * Class name.
     */
    public const CLASS_NAME = __CLASS__;

    /**
     * Translates the provided string.
     *
     * @param string $string String to be translated.
     * @param array $arguments List of translation arguments.
     *
     * @return string Translated string.
     */
    public function translate(string $string, array $arguments = []): string;

    /**
     * Returns current system language
     *
     * @return string
     */
    public function getSystemLanguage(): string;
}
