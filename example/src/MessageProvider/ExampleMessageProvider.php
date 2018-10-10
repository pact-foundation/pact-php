<?php

namespace MessageProvider;

class ExampleMessageProvider
{
    /** @var array */
    private $metadata;

    /**
     * @var mixed
     */
    private $contents;

    public function __construct($metadata = [])
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
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     *
     * @return ExampleMessageProvider
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     *  Build metadata and content for message
     *
     * @return string
     */
    public function Build()
    {
        $obj           = new \stdClass();
        $obj->metadata = $this->metadata;
        $obj->contents = $this->contents;

        return \json_encode($obj);
    }
}
