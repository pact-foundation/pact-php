<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

trait ResponsePartTrait
{
    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Response');
    }
}
