<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class ReturnsPage
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class ReturnsPage extends DataTransferObject
{
    /**
     * @var int
     */
    private $totalCount;
    /**
     * @var ReturnResponse[]
     */
    private $returns;

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    /**
     * @return ReturnResponse[]
     */
    public function getReturns()
    {
        return $this->returns;
    }

    /**
     * @param ReturnResponse[] $returns
     */
    public function setReturns($returns)
    {
        $this->returns = $returns;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $returns = [];

        foreach ($this->returns as $return) {
            $returns[] = $return->toArray();
        }

        return [
            'TotalCount' => $this->totalCount,
            'Content' => $returns,
        ];
    }

    public static function fromArray(array $data)
    {
       $page = new self();

       $page->setTotalCount(static::getDataValue($data, 'TotalCount'));
       $page->setReturns(ReturnResponse::fromBatch(static::getDataValue($data, 'Content', [])));

       return $page;
    }
}