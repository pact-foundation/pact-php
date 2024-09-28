<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\Exception\QueryParameterNotAddedException;
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
                $success = $this->client->withQueryParameterV2($interaction->getHandle(), (string) $key, (int) $index, (string) $value);
                if (!$success) {
                    throw new QueryParameterNotAddedException('Mock server has been started, interaction handle is invalid, or empty query parameter name');
                }
            }
        }
    }

    private function withRequest(Interaction $interaction): void
    {
        $request = $interaction->getRequest();
        $success = $this->client->withRequest($interaction->getHandle(), $request->getMethod(), $request->getPath());
        if (!$success) {
            throw new QueryParameterNotAddedException('Mock server has been started, interaction handle is invalid, or empty query parameter name');
        }
    }
}
