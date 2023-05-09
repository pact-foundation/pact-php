<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

use PhpPact\Consumer\Driver\Interaction\Contents\ContentsDriverInterface;
use PhpPact\Consumer\Driver\Interaction\Contents\ResponseBodyDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\FFI\ClientInterface;

class ResponseDriver extends AbstractPartDriver implements ResponseDriverInterface
{
    public function __construct(
        ClientInterface $client,
        InteractionDriverInterface $interactionDriver,
        ?ContentsDriverInterface $responseBodyDriver = null
    ) {
        parent::__construct($client, $interactionDriver, $responseBodyDriver ?? new ResponseBodyDriver($client, $interactionDriver));
    }

    public function withResponse(int $status): self
    {
        $this->client->call('pactffi_response_status', $this->getInteractionId(), $status);

        return $this;
    }

    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Response');
    }
}
