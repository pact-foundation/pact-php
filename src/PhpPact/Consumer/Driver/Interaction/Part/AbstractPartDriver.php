<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

use PhpPact\Consumer\Driver\Interaction\Contents\ContentsDriverInterface;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractPartDriver implements PartDriverInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected InteractionDriverInterface $interactionDriver,
        private ContentsDriverInterface $contentsDriver
    ) {
    }

    public function withBody(?string $contentType = null, ?string $body = null): self
    {
        $this->contentsDriver->withContents($contentType, $body);

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
        return $this->interactionDriver->getId();
    }

    abstract protected function getPart(): int;
}
