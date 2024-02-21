<?php

namespace PhpPact\FFI;

use PhpPact\Consumer\Driver\Enum\InteractionPart;

class Lib implements LibInterface
{
    public function __construct(private ClientInterface $client)
    {
    }

    public function getInteractionPartId(InteractionPart $part): int
    {
        return match ($part) {
            InteractionPart::REQUEST => $this->client->get('InteractionPart_Request'),
            InteractionPart::RESPONSE => $this->client->get('InteractionPart_Response'),
        };
    }
}
