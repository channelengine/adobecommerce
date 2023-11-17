<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Responses;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class AcknowledgeResponse
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Responses
 */
class AcknowledgeResponse extends DataTransferObject
{
    /**
     * @var int
     */
    protected $statusCode;
    /**
     * @var bool
     */
    protected $success;
    /**
     * @var string
     */
    protected $message;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'StatusCode' => $this->getStatusCode(),
            'Success' => $this->isSuccess(),
            'Message' => $this->getMessage(),
        ];
    }

    /**
     *
     * Creates instance of the data transfer object from an array.
     *
     * @param array $data Raw data used for the object instantiation.
     *
     * @return AcknowledgeResponse
     */
    public static function fromArray(array $data)
    {
        $response = new self();
        $response->setStatusCode(static::getDataValue($data, 'StatusCode', null));
        $response->setSuccess(static::getDataValue($data, 'Success', null));
        $response->setMessage(static::getDataValue($data, 'Message', null));

        return $response;
    }
}