<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class StockLocation
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Returns\DTO
 */
class StockLocation extends DataTransferObject
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string | null
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
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
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
        $location = new self();

        $location->setId(static::getDataValue($data, 'Id', 0));
        $location->setName(static::getDataValue($data, 'Name'));

        return $location;
    }
}