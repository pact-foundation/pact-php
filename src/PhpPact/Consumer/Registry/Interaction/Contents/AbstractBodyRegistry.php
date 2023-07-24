<?php

namespace PhpPact\Consumer\Registry\Interaction\Contents;

use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\FFI\ClientInterface;

abstract class AbstractBodyRegistry implements ContentsRegistryInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected InteractionRegistryInterface $interactionRegistry
    ) {
    }

    public function withContents(?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->client->call('pactffi_with_body', $this->interactionRegistry->getId(), $this->getPart(), $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    abstract protected function getPart(): int;
}
