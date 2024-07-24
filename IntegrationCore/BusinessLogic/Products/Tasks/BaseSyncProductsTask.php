<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Contracts\Context;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Notifications\Traits\NotificationCreator;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Contracts\ProductsSyncConfigService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Product;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain\Variant;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Transformers\ApiProductTransformer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Contracts\DetailsService;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Tasks\TransactionalTask;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\BaseException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Exceptions\PurgeException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpCommunicationException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Http\Exceptions\HttpRequestException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Logger\Logger;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Serializer\Serializer;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class BaseSyncProductsTask
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Tasks
 */
abstract class BaseSyncProductsTask extends TransactionalTask
{
    use NotificationCreator;

    /**
     * @var string[]
     */
    protected $productIds;
    /**
     * @var int
     */
    protected $reconfiguredBatchSize = 0;
    /**
     * @var array
     */
    protected $exportedProductIds = [];
    /**
     * @var array
     */
    protected $exportedVariantIds = [];
    /**
     * @var int
     */
    protected $syncedNumber = 0;
    /**
     * @var int
     */
    protected $totalNumberOfProducts = 0;
    /**
     * @var int
     */
    protected $failedNumber = 0;

    /**
     * ProductsUpsertTask constructor.
     *
     * @param string[] $productIds
     */
    public function __construct(array $productIds)
    {
        $this->productIds = $productIds;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'productIds' => $this->productIds,
            'reconfiguredBatchSize' => $this->reconfiguredBatchSize,
            'exportedProductIds' => $this->exportedProductIds,
            'exportedVariantIds' => $this->exportedVariantIds,
            'syncedNumber' => $this->syncedNumber,
            'totalNumberOfProducts' => $this->totalNumberOfProducts,
            'failedNumber' => $this->failedNumber,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $array)
    {
        $task = new static($array['productIds']);
        $task->reconfiguredBatchSize = $array['reconfiguredBatchSize'];
        $task->exportedProductIds = $array['exportedProductIds'];
        $task->exportedVariantIds = $array['exportedVariantIds'];
        $task->syncedNumber = $array['syncedNumber'];
        $task->totalNumberOfProducts = $array['totalNumberOfProducts'];
        $task->failedNumber = $array['failedNumber'];

        return $task;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return Serializer::serialize(
            [
                'productIds' => $this->productIds,
                'reconfiguredBatchSize' => $this->reconfiguredBatchSize,
                'exportedProductIds' => $this->exportedProductIds,
                'exportedVariantIds' => $this->exportedVariantIds,
                'syncedNumber' => $this->syncedNumber,
                'totalNumberOfProducts' => $this->totalNumberOfProducts,
                'failedNumber' => $this->failedNumber,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $unserialized = Serializer::unserialize($serialized);

        $this->productIds = $unserialized['productIds'];
        $this->reconfiguredBatchSize = $unserialized['reconfiguredBatchSize'];
        $this->exportedProductIds = $unserialized['exportedProductIds'];
        $this->exportedVariantIds = $unserialized['exportedVariantIds'];
        $this->syncedNumber = $unserialized['syncedNumber'];
        $this->totalNumberOfProducts = $unserialized['totalNumberOfProducts'];
        $this->failedNumber = $unserialized['failedNumber'];
    }

    /**
     * Checks whether task can be reconfigured or not.
     *
     * @return bool
     */
    public function canBeReconfigured()
    {
        return $this->reconfiguredBatchSize !== 1;
    }

    /**
     * Reconfigures task.
     */
    public function reconfigure()
    {
        if ($this->reconfiguredBatchSize === 0) {
            $this->reconfiguredBatchSize = $this->getConfigService()->getSynchronizationBatchSize();
        }

        $this->reconfiguredBatchSize = ceil($this->reconfiguredBatchSize / 2);
    }

    /**
     * Exports products to ChannelEngine API.
     */
    public function execute()
    {
        $this->initializeNumberOfProducts();
        $batch = $this->getBatchOfProductIds();

        while (!empty($batch)) {
            $batchOfProducts = [];
            $products = $this->fetchProductsToExport($batch);

            foreach ($products as $product) {
                if ($this->isProductExported($product)) {
                    continue;
                }

                if ($this->isExportBatchFull($batchOfProducts)) {
                    $this->exportBatch($batchOfProducts, count($batch));
                }

                $this->getProductExportData($batchOfProducts, $product);

                $this->markExportedProduct($product);
            }

            if (!empty($batchOfProducts)) {
                $this->exportBatch($batchOfProducts, count($batch));
            }

            $this->unsetExportedIds($batch);

            $batch = $this->getBatchOfProductIds();

            $this->reportProgress($this->getCurrentProgress());
            $this->updateTransactionLog();
        }

        $this->updateTransactionLog();

        if ($this->failedNumber > 0) {
            $this->addTaskSummary(
                $this->getTransactionLog(),
                'Products upload finished with errors.',
                $this->getTransactionLog()->getSynchronizedEntities() === 0 ? Context::ERROR : Context::WARNING
            );
        }

        $this->reportProgress(100);
    }

    /**
     * Procures array of objects which are to be exported.
     *
     * @return array
     */
    protected function fetchProductsToExport(array $batch)
    {
        return $this->getProductsService()->getProducts($batch);
    }

    /**
     * Procures and saves necessary product data inside $batchOfProducts to be
     * later used in the exportProducts($batchOfProducts) function call
     */
    protected function getProductExportData(&$batchOfProducts, $product)
    {
        $syncConfig = $this->getProductsSyncConfigService()->get();
        $threeLevelSyncStatus =  $syncConfig ? $syncConfig->getThreeLevelSyncStatus() : false;

        if ($threeLevelSyncStatus === false) {
            $batchOfProducts[$product->getId()] = ApiProductTransformer::transformProductToTwoLevel($product);
        } else {
            $batchOfProducts[$product->getId()] = count($product->getVariants()) === 0
                ? ApiProductTransformer::transformSimpleProductToThreeLevel($product)
                : ApiProductTransformer::transformProductWithChildrenToThreeLevel($product);
        }
    }

    protected function updateTransactionLog()
    {
        $numberOfSynced = $this->getTransactionLog()->getSynchronizedEntities();
        $this->getTransactionLog()->setSynchronizedEntities(
            $numberOfSynced ?
                $numberOfSynced + $this->syncedNumber : $this->syncedNumber
        );
        $this->syncedNumber = 0;
    }

    /**
     * Exports batch of products to ChannelEngine API.
     *
     * @param $batchOfProducts
     * @param $syncedProducts
     */
    protected function exportBatch(&$batchOfProducts, $syncedProducts)
    {
        try {
            $this->exportProducts($batchOfProducts, $syncedProducts);
        } catch (PurgeException $e) {
            $this->handlePurgeError($e, $batchOfProducts, $syncedProducts);
        } catch (BaseException $e) {
            $this->handleExportError($e, $batchOfProducts, $syncedProducts);
        }


        $batchOfProducts = [];
    }

    /**
     * @param $batchOfProducts
     * @param $syncedProducts
     *
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     */
    abstract protected function exportProducts(&$batchOfProducts, $syncedProducts);

    /**
     * @param BaseException $exception
     * @param array $batchOfProducts
     * @param $syncedProducts
     */
    protected function handleExportError(BaseException $exception, &$batchOfProducts, $syncedProducts)
    {
        $this->getTransactionLog()->setHasErrors(true);
        $productIndexes = $this->getProductIndexes($exception, $batchOfProducts);
        $this->createExceptionDetails($exception, $batchOfProducts, $productIndexes);

        $batchOfProducts = array_values($this->removeFailedProducts($batchOfProducts, $productIndexes));
        $syncedProducts = count($batchOfProducts) ? $syncedProducts : 0;
        try {
            if (count($batchOfProducts)) {
                $this->exportProducts($batchOfProducts, $syncedProducts);
            }
        } catch (BaseException $e) {
            $productIndexes = $this->getProductIndexes($e, $batchOfProducts);
            $this->createExceptionDetails($e, $batchOfProducts, $productIndexes);
        }
    }

    /**
     * @param PurgeException $exception
     * @param array $batchOfProducts
     * @param $syncedProducts
     */
    protected function handlePurgeError(PurgeException $exception, &$batchOfProducts, $syncedProducts)
    {
        $this->getTransactionLog()->setHasErrors(true);
        $productIndexes = $batchOfProducts;
        $this->failedNumber += count($productIndexes);
        $this->createPurgeExceptionDetails($exception, $batchOfProducts, $productIndexes);

        $batchOfProducts = array_values($this->removeFailedProducts($batchOfProducts, $productIndexes));
        $syncedProducts -= count($productIndexes);
        try {
            $this->exportProducts($batchOfProducts, $syncedProducts);
        } catch (PurgeException $e) {
            $productIndexes = $batchOfProducts;
            $this->failedNumber += count($productIndexes);
            $this->createPurgeExceptionDetails($e, $batchOfProducts, $productIndexes);
        }
    }

    /**
     * @param BaseException $exception
     * @param array $batchOfProducts
     * @param array $productIndexes
     */
    protected function createExceptionDetails(BaseException $exception, &$batchOfProducts, $productIndexes)
    {
        $error = json_decode($exception->getMessage(), true);
        $errorMessages = isset($error['errorMessages']) ? $error['errorMessages'] : [];
        $warningMessages = isset($error['warningMessages']) ? $error['warningMessages'] : [];
        $validationErrors = isset($error['validationErrors']) ? $error['validationErrors'] : [];

        foreach ($productIndexes as $key => $index) {
            if (!isset($batchOfProducts[$key])) {
                continue;
            }

            $this->logProductExceptionDetails($batchOfProducts[$key], $errorMessages, $warningMessages, $validationErrors, $index);
        }
    }

    private function logProductExceptionDetails($product, $errorMessages, $warningMessages, $validationErrors, $index) {
        if (is_array($product)) {
            foreach ($product as $singleProduct) {
                $this->addLogDetail(
                    $singleProduct->getMerchantProductNo(),
                    $this->formatErrorMessage($errorMessages, $warningMessages, $validationErrors, $index)
                );
            }
        } else if ($product instanceof \ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Products\DTO\Product) {
            $this->addLogDetail(
                $product->getMerchantProductNo(),
                $this->formatErrorMessage($errorMessages, $warningMessages, $validationErrors, $index)
            );
        }
    }

    /**
     * @param PurgeException $exception
     * @param array $batchOfProducts
     * @param array $productIndexes
     */
    protected function createPurgeExceptionDetails(PurgeException $exception, &$batchOfProducts, $productIndexes)
    {
        $error = json_decode($exception->getMessage(), true);
        $errorMessages = isset($error['errorMessages']) ? $error['errorMessages'] : [];
        $warningMessages = isset($error['warningMessages']) ? $error['warningMessages'] : [];
        $validationErrors = isset($error['validationErrors']) ? $error['validationErrors'] : [];

        foreach ($productIndexes as $key => $index) {
            if (!isset($batchOfProducts[$key])) {
                continue;
            }

            $this->addLogDetail(
                $batchOfProducts[$key],
                $this->formatErrorMessage($errorMessages, $warningMessages, $validationErrors, [$index])
            );
        }
    }

    /**
     * @param array $batchOfProducts
     * @param array $indexes
     *
     * @return array
     */
    protected function removeFailedProducts(&$batchOfProducts, $indexes)
    {
        foreach ($indexes as $key => $index) {
            unset($batchOfProducts[$key]);
        }

        return $batchOfProducts;
    }

    /**
     * @param BaseException $exception
     * @param array $batchOfProducts
     *
     * @return array
     */
    protected function getProductIndexes($exception, &$batchOfProducts)
    {
        $error = json_decode($exception->getMessage(), true);
        $errorMessages = isset($error['errorMessages']) ?
            $error['errorMessages'] : [];
        $warningMessages = isset($error['warningMessages']) ? $error['warningMessages'] : [];
        $validationErrors = isset($error['validationErrors']) ? $error['validationErrors'] : [];
        $errorKeys = array_keys($validationErrors);

        foreach (array_merge($errorMessages, $warningMessages) as $errorMessage) {
            $errorKeys[] = isset($errorMessage['Reference']) ? $errorMessage['Reference'] : '';
        }

        Logger::logError('Product synchronization failed because: ' . $exception->getMessage());

        $productIndexes = [];

        foreach ($batchOfProducts as $key => $product) {

            if (is_array($product)) {
                foreach ($product as $singleProduct) {
                    $productIndexes[$key][] = in_array((string)$singleProduct->getMerchantProductNo(), $errorKeys, true) ?
                        $singleProduct->getMerchantProductNo() : null;
                }
            } else {
                $productIndexes[$key][] = in_array((string)$product->getMerchantProductNo(), $errorKeys, true) ?
                    $product->getMerchantProductNo() : null;
            }

            if (preg_grep('/\[' . $key . '].*/', $errorKeys)) {
                foreach (preg_grep('/\[' . $key . '].*/', $errorKeys) as $item) {
                    $productIndexes[$key][] = $item;
                }
            }

            $productIndexes[$key] = array_filter($productIndexes[$key]);
        }

        return array_filter($productIndexes);
    }

    /**
     * @param array $errorMessages
     * @param array $validationErrors
     * @param array $errorKeys
     *
     * @return string
     */
    protected function formatErrorMessage($errorMessages, $warningMessages, $validationErrors, $errorKeys)
    {
        $message = '';
        $isError = false;

        foreach ($errorKeys as $key) {
            foreach ($errorMessages as $errorMessage) {
                if (isset($errorMessage['Reference']) && $errorMessage['Reference'] === (string)$key) {
                    foreach ($errorMessage['Errors'] as $error) {
                        $message .= ' ' . $error;
                        $isError = true;
                    }
                }
            }

            foreach ($warningMessages as $warningMessage) {
                if (isset($warningMessage['Reference']) && $warningMessage['Reference'] === (string)$key) {
                    foreach ($warningMessage['Warnings'] as $warning) {
                        $message .= ' ' . $warning;
                    }
                }
            }
        }

        foreach ($errorKeys as $errorKey) {
            $message .= ' ' . (isset($validationErrors[$errorKey]) ? $validationErrors[$errorKey][0] : '');
        }

        if (!$message) {
            return 'Unknown error.';
        }

        $isError ? $this->failedNumber++ : $this->syncedNumber++;

        return $isError ? 'Failed to synchronize product %s because: ' . $message : 'Product %s synchronized with warnings: ' . $message;
    }

    /**
     * Adds log details.
     */
    protected function addLogDetail($id, $message)
    {
        $this->getDetailsService()->create(
            $this->getTransactionLog(),
            $message,
            [
                $id,
            ]
        );
    }

    /**
     * @return DetailsService
     */
    protected function getDetailsService()
    {
        return ServiceRegister::getService(DetailsService::class);
    }

    /**
     * Checks if export batch is full.
     *
     * @param $batchOfProducts
     *
     * @return bool
     */
    protected function isExportBatchFull($batchOfProducts)
    {
        return count($batchOfProducts) >= $this->getBatchSize();
    }

    /**
     * Adds product id to exportedProductIds.
     *
     * @param Product $product
     */
    protected function markExportedProduct(Product $product)
    {
        $this->exportedProductIds[] = $product->getId();
    }

    /**
     * Adds variant id to exportedVariantIds.
     *
     * @param Variant $variant
     * @param Product $product
     */
    protected function markExportedVariant(Variant $variant, Product $product)
    {
        $this->exportedVariantIds[$product->getId()][] = $variant->getId();
    }

    /**
     * Checks if product is already exported.
     *
     * @param Product $product
     *
     * @return bool
     */
    protected function isProductExported(Product $product)
    {
        return in_array($product->getId(), $this->exportedProductIds, true);
    }

    /**
     * Checks if variant is already exported.
     *
     * @param Variant $variant
     * @param Product $product
     *
     * @return bool
     */
    protected function isVariantExported(Variant $variant, Product $product)
    {
        return isset($this->exportedVariantIds[$product->getId()]) &&
            in_array($variant->getId(), $this->exportedVariantIds[$product->getId()], true);
    }

    /**
     * Splits product ids into batches.
     *
     * @return array
     */
    protected function getBatchOfProductIds()
    {
        return array_slice($this->productIds, 0, $this->getBatchSize(), true);
    }

    /**
     * Returns current batch size.
     *
     * @return int
     */
    protected function getBatchSize()
    {
        if ($this->reconfiguredBatchSize !== 0) {
            return $this->reconfiguredBatchSize;
        }

        return $this->getConfigService()->getSynchronizationBatchSize();
    }

    /**
     * Unsets exported product ids.
     *
     * @param array $ids
     */
    protected function unsetExportedIds($ids)
    {
        foreach ($this->productIds as $key => $productId) {
            if (in_array($productId, $ids, true)) {
                unset($this->productIds[$key]);
            }
        }
    }

    /**
     * Retrieves current progress.
     *
     * @return float
     */
    protected function getCurrentProgress()
    {
        return ($this->syncedNumber * 95.0) / $this->totalNumberOfProducts;
    }

    /**
     * Initializes total number of products.
     *
     * @return int
     */
    protected function initializeNumberOfProducts()
    {
        if ($this->totalNumberOfProducts === 0) {
            $this->totalNumberOfProducts = count($this->productIds);
        }

        return $this->totalNumberOfProducts ?: 1;
    }

    /**
     * Retrieves instance of ProductsService.
     *
     * @return ProductsService
     */
    protected function getProductsService()
    {
        return ServiceRegister::getService(ProductsService::class);
    }

    /**
     * Retrieves instance of ProductsSyncConfigService.
     *
     * @return ProductsSyncConfigService
     */
    protected function getProductsSyncConfigService()
    {
        return ServiceRegister::getService(ProductsSyncConfigService::class);
    }

    /**
     * Retrieves instance of product Proxy.
     *
     * @return Proxy
     */
    protected function getProductsProxy()
    {
        return ServiceRegister::getService(Proxy::class);
    }
}
