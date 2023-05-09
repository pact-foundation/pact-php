<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

trait ResponsePartTrait
{
    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Response');
    }
}
