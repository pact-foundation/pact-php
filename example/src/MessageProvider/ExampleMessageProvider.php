<?php

namespace MessageProvider;

class ExampleMessageProvider
{
    /** @var array */
    private array $metadata;

    /**
     * @var mixed
     */
    private mixed $contents;

    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     *
     * @return ExampleMessageProvider
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContents(): mixed
    {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     *
     * @return ExampleMessageProvider
     */
    public function setContents(mixed $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     *  Build metadata and content for message
     *
     * @return string
     */
    public function Build(): string
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;
        $obj->contents = $this->contents;

        return \json_encode($obj);
    }
}
