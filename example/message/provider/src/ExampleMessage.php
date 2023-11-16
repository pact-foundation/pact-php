<?php

namespace MessageProvider;

class ExampleMessage
{
    public function __construct(private mixed $contents, private array $metadata = [])
    {
    }

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
        return json_encode([
            'metadata' => $this->metadata,
            'contents' => $this->contents,
        ]);
    }
}
