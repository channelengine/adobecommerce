<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain;

/**
 * Class CustomAttribute
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Products\Domain
 */
class CustomAttribute
{
    const TYPE_TEXT = 'TEXT';
    const TYPE_NUMBER = 'NUMBER';
    const TYPE_URL = 'URL';
    const TYPE_IMG_URL = 'IMAGEURL';

    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $value;
    /**
     * @var string One of [ TEXT, NUMBER, URL, IMAGEURL ]
     */
    protected $type;
    /**
     * @var bool
     */
    protected $isPublic;

    /**
     * CustomAttribute constructor.
     *
     * @param string $key
     * @param string $value
     * @param string $type
     * @param bool $isPublic
     */
    public function __construct($key, $value, $type = self::TYPE_TEXT, $isPublic = false)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->isPublic = $isPublic;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }
}