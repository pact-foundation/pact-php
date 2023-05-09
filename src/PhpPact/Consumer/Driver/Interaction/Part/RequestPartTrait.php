<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

trait RequestPartTrait
{
    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Request');
    }
}
