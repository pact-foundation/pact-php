<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;

class RequestDriver extends AbstractInteractionPartDriver implements RequestDriverInterface
{
    public function registerRequest(Interaction $interaction): void
    {
        $this->withBody($interaction, InteractionPart::REQUEST);
        $this->withHeaders($interaction, InteractionPart::REQUEST);
        $this->withQueryParameters($interaction);
        $this->withRequest($interaction);
    }

    private function withQueryParameters(Interaction $interaction): void
    {
        foreach ($interaction->getRequest()->getQuery() as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_query_parameter_v2', $interaction->getHandle(), (string) $key, (int) $index, (string) $value);
            }
        }
    }

    private function withRequest(Interaction $interaction): void
    {
        $request = $interaction->getRequest();
        $this->client->call('pactffi_with_request', $interaction->getHandle(), $request->getMethod(), $request->getPath());
    }
}
