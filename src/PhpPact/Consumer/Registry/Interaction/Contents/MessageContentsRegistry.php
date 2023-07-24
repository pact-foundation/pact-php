<?php

namespace PhpPact\Consumer\Registry\Interaction\Contents;

use PhpPact\Consumer\Registry\Interaction\MessageRegistryInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Model\StringData;

class MessageContentsRegistry implements ContentsRegistryInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected MessageRegistryInterface $messageRegistry
    ) {
    }

    public function withContents(?string $contentType = null, ?string $contents = null): void
    {
        if (is_null($contents)) {
            return;
        }
        $data = StringData::createFrom($contents);
        $this->client->call('pactffi_message_with_contents', $this->messageRegistry->getId(), $contentType, $data->getValue(), $data->getSize());
    }
}
