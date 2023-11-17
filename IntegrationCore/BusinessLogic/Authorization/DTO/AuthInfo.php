<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class AuthInfo
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Authorization\DTO
 */
class AuthInfo extends DataTransferObject
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;

    /**
     * @var string
     */
    protected $accountName;
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * AuthInfo constructor.
     *
     * @param string $accountName
     * @param string $apiKey
     */
    public function __construct($accountName, $apiKey)
    {
        $this->accountName = $accountName;
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * @param string $accountName
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Creates instance of AuthInfo.
     *
     * @param array $data
     *
     * @return AuthInfo
     */
    public static function fromArray(array $data)
    {
        return new self($data['account_name'], $data['api_key']);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return array(
            'account_name' => $this->getAccountName(),
            'api_key' => $this->getApiKey(),
        );
    }
}