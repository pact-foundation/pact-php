<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Ruby Standalone Mock Server for PACT tests.
 * Class Interaction.
 */
class Message
{
    use ProviderStates;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private array $metadata;

    /**
     * @var mixed
     */
    private $contents;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Message
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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
     * @return Message
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
     * @return Message
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }
}
