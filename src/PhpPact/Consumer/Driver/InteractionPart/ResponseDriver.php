<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;

class ResponseDriver extends AbstractInteractionPartDriver implements ResponseDriverInterface
{
    public function registerResponse(Interaction $interaction): void
    {
        // @todo Fix 'Exception: String could not be parsed as XML' in xml's consumer test
        // when calling `withBody` before `withHeaders`
        $this->withHeaders($interaction, InteractionPart::RESPONSE);
        $this->withBody($interaction, InteractionPart::RESPONSE);
        $this->withResponse($interaction);
    }

    private function withResponse(Interaction $interaction): void
    {
        $this->client->call('pactffi_response_status_v2', $interaction->getHandle(), $interaction->getResponse()->getStatus());
    }
}
