<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Domain;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class CreateResponse
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Orders\Domain
 */
class CreateResponse extends DataTransferObject
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string
     */
    protected $shopOrderId;
	/**
	 * @var string
	 */
    protected $message;

    /**
     * @return bool
     */
    public function getSuccess()
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
    public function getShopOrderId()
    {
        return $this->shopOrderId;
    }

    /**
     * @param string $shopOrderId
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->shopOrderId = $shopOrderId;
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
	public function setMessage( $message )
	{
		$this->message = $message;
	}

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'success' => $this->getSuccess(),
            'shopOrderId' => $this->getShopOrderId(),
	        'message' => $this->getMessage(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $response = new self();
        $response->setSuccess(static::getDataValue($data, 'success', false));
        $response->setShopOrderId(static::getDataValue($data, 'shopOrderId', ''));
        $response->setMessage(static::getDataValue($data, 'message', ''));

        return $response;
    }
}