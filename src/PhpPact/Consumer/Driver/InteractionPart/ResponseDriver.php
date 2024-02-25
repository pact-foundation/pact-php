<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;

class ResponseDriver extends AbstractInteractionPartDriver implements ResponseDriverInterface
{
    public function registerResponse(Interaction $interaction): void
    {
        $this
            ->withResponse($interaction)
            ->withHeaders($interaction, InteractionPart::RESPONSE)
            ->withBody($interaction, InteractionPart::RESPONSE);
    }

    private function withResponse(Interaction $interaction): self
    {
        $this->client->call('pactffi_response_status_v2', $interaction->getHandle(), $interaction->getResponse()->getStatus());

        return $this;
    }
}
