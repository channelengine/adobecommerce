<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

class Webhook extends DataTransferObject
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var bool
     */
    protected $isActive;
    /**
     * @var array @see ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Webhooks\Enums\EventTypes
     */
    protected $events;

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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param array $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }

    public function toArray()
    {
        return [
            'Name' => $this->getName(),
            'Url' => $this->getUrl(),
            'IsActive' => $this->isActive(),
            'Events' => $this->getEvents(),
        ];
    }

    public static function fromArray(array $data)
    {
        $entity = new static();
        $entity->setName(static::getDataValue($data, "Name"));
        $entity->setUrl(static::getDataValue($data, 'Url'));
        $entity->setIsActive(static::getDataValue($data, 'IsActive', false));
        $entity->setEvents(static::getDataValue($data, 'Events', []));

        return $entity;
    }
}