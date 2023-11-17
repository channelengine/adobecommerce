<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class MerchantReturnUpdate
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class MerchantReturnUpdate extends DataTransferObject
{
    /**
     * @var int
     */
    private $returnId;
    /**
     * @var Line[]
     */
    private $lines;

    /**
     * @return int
     */
    public function getReturnId()
    {
        return $this->returnId;
    }

    /**
     * @param int $returnId
     */
    public function setReturnId($returnId)
    {
        $this->returnId = $returnId;
    }

    /**
     * @return Line[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param Line[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $lines = [];

        foreach ($this->lines as $line) {
            $lines[] = $line->toArray();
        }

        return [
            'ReturnId' => $this->returnId,
            'Lines' => $lines,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $returnUpdate = new self();

        $returnUpdate->setReturnId(static::getDataValue($data, 'ReturnId', 0));
        $returnUpdate->setLines(Line::fromBatch(static::getDataValue($data, 'Lines', [])));

        return $returnUpdate;
    }
}