<?php

namespace PhpPact\Consumer\Driver\Interaction\Contents;

class RequestBodyDriver extends AbstractBodyDriver
{
    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Request');
    }
}
