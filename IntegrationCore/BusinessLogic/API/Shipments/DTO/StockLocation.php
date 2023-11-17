<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class StockLocation
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Shipments\DTO
 */
class StockLocation extends DataTransferObject
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'Id' => $this->id,
            'Name' => $this->name,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $stockLocation = new self();
        $stockLocation->setId(static::getDataValue($data, 'Id', 0));
        $stockLocation->setName(static::getDataValue($data, 'Name'));

        return $stockLocation;
    }
}