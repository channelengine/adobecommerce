<?php

namespace ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ObjectManager;

/**
 * Class TranslationService
 *
 * @package ChannelEngine\ChannelEngineIntegration\Services\BusinessLogic
 */
class TranslationService implements Contracts\TranslationService
{
    public const CORE_STRINGS = [
        'loginRequiredFields' => 'API key and Account name fields are required.',
        'invalidCredentials' => 'Invalid API key or Account name.',
        'initialSyncFail' => 'Failed to start initial sync because %s',
        'configSaved' => 'Configuration saved successfully.',
        'invalidQuantity' => 'Stock quantity is not valid.',
        'invalidOrderStatuses' => 'Order statuses are not valid.',
        'invalidValues' => 'Invalid values.',
        'defaultQuantityRequired' => 'Default stock quantity is required field.',
        "PRODUCT_DEFECT" => "CE Defective product",
        "PRODUCT_UNSATISFACTORY" => "CE Unsatisfactory product",
        "WRONG_PRODUCT" => "CE Wrong product",
        "TOO_MANY_PRODUCTS" => "CE Too many products",
        "REFUSED" => "CE Refused",
        "REFUSED_DAMAGED" => "CE Refused (damaged)",
        "WRONG_ADDRESS" => "CE Wrong address",
        "NOT_COLLECTED" => "CE Not collected",
        "WRONG_SIZE" => "CE Wrong size",
        "OTHER" => "CE Other",
    ];

    /**
     * Translates the provided string.
     *
     * @param string $string String to be translated.
     * @param array $arguments List of translation arguments.
     *
     * @return string Translated string.
     */
    public function translate(string $string, array $arguments = []): string
    {
        if (array_key_exists($string, self::CORE_STRINGS)) {
            $translatedString = __(self::CORE_STRINGS[$string]);
        } else {
            $translatedString = __($string);
        }

        return vsprintf($translatedString, $arguments);
    }

    /**
     * Returns current system language.
     *
     * @return string
     */
    public function getSystemLanguage(): string
    {
        $locale = $this->getLocale();
        $language = explode('_', $locale);

        return $language[0];
    }

    /**
     * Returns lang
     *
     * @return string
     */
    private function getLocale(): string
    {
        $om = ObjectManager::getInstance();
        /** @var Session $session */
        $session = $om->get(Session::class);
        $user = $session->getUser();

        if ($user === null) {
            $locale = 'en_US';
        } else {
            $locale = $user->getInterfaceLocale() ?: '';
        }

        return $locale;
    }
}
