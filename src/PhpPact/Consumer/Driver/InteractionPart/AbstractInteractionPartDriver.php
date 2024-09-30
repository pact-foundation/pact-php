<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriver;
use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\Exception\HeaderNotAddedException;
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

    protected function withBody(Interaction $interaction, InteractionPart $part): void
    {
        $this->bodyDriver->registerBody($interaction, $part);
    }

    protected function withHeaders(Interaction $interaction, InteractionPart $interactionPart): void
    {
        $headers = $interaction->getHeaders($interactionPart);
        $partId = match ($interactionPart) {
            InteractionPart::REQUEST => $this->client->getInteractionPartRequest(),
            InteractionPart::RESPONSE => $this->client->getInteractionPartResponse(),
        };
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $success = $this->client->withHeaderV2($interaction->getHandle(), $partId, (string) $header, (int) $index, (string) $value);
                if (!$success) {
                    throw new HeaderNotAddedException('Mock server has been started, interaction handle is invalid, or empty header name');
                }
            }
        }
    }
}
