<?php

namespace PhpPactTest\CompatibilitySuite\Model;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;

class Message
{
    private null|Binary|Text $body;
    /**
     * @var ?array<string, MatcherInterface|string>
     */
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

    /**
     * @return ?array<string, MatcherInterface|string>
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @param ?array<string, MatcherInterface|string> $metadata
     */
    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function hasMetadata(): bool
    {
        return null !== $this->metadata;
    }
}
