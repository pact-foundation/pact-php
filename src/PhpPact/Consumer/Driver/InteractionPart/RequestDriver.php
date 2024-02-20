<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;

class RequestDriver extends AbstractInteractionPartDriver implements RequestDriverInterface
{
    public function registerRequest(Interaction $interaction): void
    {
        $this
            ->withRequest($interaction)
            ->withQueryParameters($interaction)
            ->withHeaders($interaction, InteractionPart::REQUEST)
            ->withBody($interaction, InteractionPart::REQUEST);
    }

    private function withQueryParameters(Interaction $interaction): self
    {
        foreach ($interaction->getRequest()->getQuery() as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_query_parameter_v2', $interaction->getHandle(), (string) $key, (int) $index, (string) $value);
            }
        }

        return $this;
    }

    private function withRequest(Interaction $interaction): self
    {
        $request = $interaction->getRequest();
        $this->client->call('pactffi_with_request', $interaction->getHandle(), $request->getMethod(), $request->getPath());

        return $this;
    }
}
