<?php

namespace PhpPactTest\CompatibilitySuite\Model;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;

class Message
{
    private null|Binary|Text $body;
    private ?array $metadata;

    public function getBody(): null|Binary|Text
    {
        return $this->body;
    }

    public function setBody(null|Binary|Text $body): void
    {
        $this->body = $body;
    }

    public function hasBody(): bool
    {
        return null !== $this->body;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function hasMetadata(): bool
    {
        return null !== $this->metadata;
    }
}
