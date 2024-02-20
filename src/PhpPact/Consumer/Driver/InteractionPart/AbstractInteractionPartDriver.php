<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriver;
use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\FFI\ClientInterface;

abstract class AbstractInteractionPartDriver
{
    private InteractionBodyDriverInterface $bodyDriver;

    public function __construct(
        protected ClientInterface $client,
        ?InteractionBodyDriverInterface $bodyDriver = null
    ) {
        $this->bodyDriver = $bodyDriver ?? new InteractionBodyDriver($client);
    }

    protected function withBody(Interaction $interaction, InteractionPart $part): self
    {
        $this->bodyDriver->registerBody($interaction, $part);

        return $this;
    }

    protected function withHeaders(Interaction $interaction, InteractionPart $interactionPart): self
    {
        $headers = $interaction->getHeaders($interactionPart);
        $partId = match ($interactionPart) {
            InteractionPart::REQUEST => $this->client->get('InteractionPart_Request'),
            InteractionPart::RESPONSE => $this->client->get('InteractionPart_Response'),
        };
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_header_v2', $interaction->getHandle(), $partId, (string) $header, (int) $index, (string) $value);
            }
        }

        return $this;
    }
}
