<?php

namespace PhpPact\Consumer\Driver\Interaction\Contents;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;
use PhpPact\FFI\ClientInterface;

abstract class AbstractBodyDriver implements ContentsDriverInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected InteractionDriverInterface $interactionDriver
    ) {
    }

    public function withContents(?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->client->call('pactffi_with_body', $this->interactionDriver->getId(), $this->getPart(), $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    abstract protected function getPart(): int;
}
