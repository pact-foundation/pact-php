<?php

namespace MessageProvider;

class ExampleMessageProvider
{
    private array $metadata;

    private mixed $contents;

    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

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

    /**
     *  Build metadata and content for message
     */
    public function Build(): string
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;
        $obj->contents = $this->contents;

        return \json_encode($obj);
    }
}
