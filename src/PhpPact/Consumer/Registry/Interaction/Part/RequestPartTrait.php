<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

trait RequestPartTrait
{
    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Request');
    }
}
