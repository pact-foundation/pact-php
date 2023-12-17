<?php

namespace PhpPact\SyncMessage\Registry\Interaction\Contents;

use PhpPact\Consumer\Registry\Interaction\Body\BodyRegistryInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\SyncMessage\Exception\InteractionContentNotAddedException;

abstract class AbstractContentsRegistry implements BodyRegistryInterface
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function withContents(?string $contentType = null, ?string $contents = null): void
    {
        if (is_null($contents) || is_null($contentType)) {
            // Pact Plugin require content type to be set, or it will panic.
            return;
        }
        $error = $this->client->call('pactffi_interaction_contents', $this->getInteractionId(), $this->getPart(), $contentType, $contents);
        if ($error) {
            throw new InteractionContentNotAddedException($error);
        }
    }

    abstract protected function getInteractionId(): int;

    abstract protected function getPart(): int;
}
