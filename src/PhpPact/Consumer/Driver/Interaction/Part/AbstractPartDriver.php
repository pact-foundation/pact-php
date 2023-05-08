<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\FFI\ClientInterface;

abstract class AbstractPartDriver implements PartDriverInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected InteractionDriverInterface $interactionDriver
    ) {
    }

    public function withBody(?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->client->call('pactffi_with_body', $this->getInteractionId(), $this->getPart(), $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    public function withHeaders(array $headers): void
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_header_v2', $this->getInteractionId(), $this->getPart(), (string) $header, (int) $index, (string) $value);
            }
        }
    }

    protected function getInteractionId(): int
    {
        return $this->interactionDriver->getId();
    }

    abstract protected function getPart(): int;
}
