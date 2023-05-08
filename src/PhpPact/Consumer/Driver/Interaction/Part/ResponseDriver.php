<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

class ResponseDriver extends AbstractPartDriver implements ResponseDriverInterface
{
    public function withResponse(int $status): void
    {
        $this->client->call('pactffi_response_status', $this->getInteractionId(), $status);
    }

    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Response');
    }
}
