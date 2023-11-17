<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Authorization\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class AccountInfo
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Authorization\DTO
 */
class AccountInfo extends DataTransferObject
{
    /**
     * Account name.
     *
     * @var string
     */
    protected $name;
    /**
     * Account currency code.
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'currencyCode' => $this->currencyCode,
        ];
    }

    /**
     * Creates instance of the data transfer object from an array.
     *
     * @param array $data
     *
     * @return AccountInfo
     */
    public static function fromArray(array $data)
    {
        $accountInfo = new self();

        if (empty($data['Content'])) {
            return $accountInfo;
        }

        $accountInfo->setName(static::getDataValue($data['Content'], 'Name', ''));
        $accountInfo->setCurrencyCode(static::getDataValue($data['Content'], 'CurrencyCode', ''));

        return $accountInfo;
    }
}
