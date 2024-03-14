<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Traits;

trait GetPostParamsTrait
{
    /**
     * Retrieves post parameters.
     *
     * @return array
     */
    public function getPostParams(): array
    {
        return json_decode($this->_request->getContent(), true);
    }
}
