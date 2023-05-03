<?php

namespace PhpPact\Consumer\Model;

/**
 * Message metadata and contents to be posted to the Mock Server for PACT tests.
 */
class Message
{
    use ProviderStates;

    private string $description;

    /**
     * @var array<string, string>
     */
    private array $metadata;

    private mixed $contents;

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<string, string> $metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getContents(): mixed
    {
        return $this->contents;
    }

    public function setContents(mixed $contents): self
    {
        $this->contents = $contents;

        return $this;
    }
}
