<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

use PhpPact\Consumer\Registry\Interaction\Contents\ContentsRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractPartRegistry implements PartRegistryInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected InteractionRegistryInterface $interactionRegistry,
        private ContentsRegistryInterface $contentsRegistry
    ) {
    }

    public function withBody(?string $contentType = null, ?string $body = null): self
    {
        $this->contentsRegistry->withContents($contentType, $body);

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_header_v2', $this->getInteractionId(), $this->getPart(), (string) $header, (int) $index, (string) $value);
            }
        }

        return $this;
    }

    protected function getInteractionId(): int
    {
        return $this->interactionRegistry->getId();
    }

    abstract protected function getPart(): int;
}
