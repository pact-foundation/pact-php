<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Ruby Standalone Mock Server for PACT tests.
 * Class Interaction.
 */
class Message implements \JsonSerializable
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $providerState;

    /**
     * @var array
     */
    private $metadata;

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
     * @return Message
     */
    public function setDescription(string $description): Message
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getProviderState(): string
    {
        return $this->providerState;
    }

    /**
     * @param string $providerState
     * @return Message
     */
    public function setProviderState(string $providerState): Message
    {
        $this->providerState = $providerState;
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
     * @return Message
     */
    public function setMetadata(array $metadata): Message
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
     * @return Message
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $out = array();
        $out['description'] = $this->getDescription();

        if ($this->providerState) {
            $out['providerState'] = $this->getProviderState();
        }

        if ($this->metadata) {
            $out['metadata'] = $this->getMetadata();
        }

        if ($this->contents) {
            $out['contents'] = $this->getContents();
        }

        return $out;
    }
}
