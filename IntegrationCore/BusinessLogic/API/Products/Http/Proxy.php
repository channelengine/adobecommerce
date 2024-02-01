<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\Http;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Authorized\AuthorizedProxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\DTO\HttpRequest;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO\Product;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO\Product as APIProduct;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\Transformer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class Proxy
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\Http
 */
class Proxy extends AuthorizedProxy
{
    /**
     * Deletes product.
     *
     * @param $merchantProductNumber
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws HttpRequestException
     */
    public function deleteProduct($merchantProductNumber)
    {
        $request = new HttpRequest("products/$merchantProductNumber");

        $this->delete($request);
    }

    /**
     * Deletes products in bulk.
     *
     * @param array $merchantProductNumbers
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function bulkDelete(array $merchantProductNumbers)
    {
        $request = new HttpRequest('products/bulkdelete');
        $request->setBody($merchantProductNumbers);

        $this->post($request);
    }

    /**
     * Uploads batch of products.
     *
     * @param Product[] $products
     *
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws HttpRequestException
     */
    public function upload(array $products)
    {
        $request = new HttpRequest('products', Transformer::batchTransform($products));

        $this->post($request);
    }

	/**
	 * Uploads batch of products.
	 *
	 * @param Product[] $products
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	public function uploadWithoutStock(array $products)
	{
		$request = new HttpRequest('products', Transformer::batchTransform($products), ['ignoreStock' => 'true']);

		$this->post($request);
	}

    /**
     * Deletes products and all their related data, including its descendants, parent, and grandparent.
     *
     * @param $merchantProductNo
     * @param APIProduct[] $arrayOfAPIProducts
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function purgeAndReplaceProducts($merchantProductNo, array $arrayOfAPIProducts)
    {
        $request = new HttpRequest('products/' . $merchantProductNo . '/replace');
        $request->setBody(Transformer::batchTransform($arrayOfAPIProducts));

        $this->post($request);
    }

    /**
     * Deletes products and all their related data, including its descendants, parent, and grandparent.
     *
     * @param $merchantProductNo
     * @param APIProduct[] $arrayOfAPIProducts
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function purgeAndReplaceProductsWithoutStock($merchantProductNo, array $arrayOfAPIProducts)
    {
        $request = new HttpRequest('products/' . $merchantProductNo . '/replace');
        $request->setBody(Transformer::batchTransform($arrayOfAPIProducts));
        $request->setHeaders(['ignoreStock' => 'true']);

        $this->post($request);
    }

    /**
     * Purges entire product structure regardless of whether a child, parent, or grandparent {merchantProductNo} is passed in the body.
     *
     * @param array $arrayOfMerchantProductNumbers
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function purgeProducts(array $arrayOfMerchantProductNumbers)
    {
        $request = new HttpRequest('products/purge');
        $request->setBody(['MerchantProductNos' => $arrayOfMerchantProductNumbers]);

        $this->post($request);
    }
}