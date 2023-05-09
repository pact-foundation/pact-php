<?php

namespace PhpPact\Consumer\Driver\Interaction\Contents;

class ResponseBodyDriver extends AbstractBodyDriver
{
    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Response');
    }
}
