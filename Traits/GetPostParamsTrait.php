<?php

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
        return json_decode(file_get_contents('php://input'), true);
    }
}
