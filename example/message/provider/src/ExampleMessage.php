<?php

namespace MessageProvider;

class ExampleMessage
{
    /**
     * @param array<array-key, mixed> $metadata
     */
    public function __construct(private mixed $contents, private array $metadata = [])
    {
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getContents(): mixed
    {
        return $this->contents;
    }

    public function __toString(): string
    {
        return (string) json_encode([
            'metadata' => $this->metadata,
            'contents' => $this->contents,
        ]);
    }
}
