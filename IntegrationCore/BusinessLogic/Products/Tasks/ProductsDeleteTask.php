<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Exception;

/**
 * Class ProductsDeleteTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks
 */
class ProductsDeleteTask extends TransactionalTask
{
    /**
     * @var string[]
     */
    protected $productIds;

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return ['productIds' => $this->productIds];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        return new static($array['productIds']);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return Serializer::serialize(['productIds' => $this->productIds]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $unserialized = Serializer::unserialize($serialized);

        $this->productIds = $unserialized['productIds'];
    }

    /**
     * ProductsDeleteTask constructor.
     * @param string[] $productIds
     */
    public function __construct(array $productIds)
    {
        $this->productIds = $productIds;
    }

	/**
	 * Deletes products.
	 *
	 * @throws Exception
	 */
    public function execute()
    {
        try {
            $this->getProxy()->bulkDelete($this->productIds);
            $this->reportProgress(100);
            $this->getTransactionLog()->setSynchronizedEntities(count($this->productIds));
        } catch (BaseException $e) {
            if ($e->getCode() === 404) {
                // Product(s) have already been deleted.
                $this->reportProgress(100);

                return;
            }

            $this->getTransactionLog()->setHasErrors(true);
            $this->getDetailsService()->create($this->getTransactionLog(), 'Failed to delete product(s) because: %s', [$e->getMessage()]);
            throw new Exception('Product delete failed because: ' . $e->getMessage());
        }
    }

    /**
     * Provides proxy.
     *
     * @return Proxy
     */
    protected function getProxy()
    {
        return ServiceRegister::getService(Proxy::class);
    }

    /**
     * Provides details service.
     *
     * @return DetailsService
     */
    protected function getDetailsService()
    {
        return ServiceRegister::getService(DetailsService::class);
    }
}