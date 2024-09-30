<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;

class ResponseDriver extends AbstractInteractionPartDriver implements ResponseDriverInterface
{
    public function registerResponse(Interaction $interaction): void
    {
        $this->withBody($interaction, InteractionPart::RESPONSE);
        $this->withHeaders($interaction, InteractionPart::RESPONSE);
        $this->withResponse($interaction);
    }

    private function withResponse(Interaction $interaction): void
    {
        $this->client->responseStatusV2($interaction->getHandle(), $interaction->getResponse()->getStatus());
    }
}
