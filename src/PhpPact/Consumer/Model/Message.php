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
     * @var array
     */
    private $providerStates = [];

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
    public function getProviderStates(): array
    {
        return $this->providerStates;
    }

    /**
     * @param mixed $overwrite
     *
     * @return array
     */
    public function setProviderState(string $name, array $params = [], $overwrite = true): array
    {
        $this->addProviderState($name, $params, $overwrite);

        return $this->providerStates;
    }

    /**
     * @param string $name
     * @param array  $params
     * @param bool   $overwrite - if true reset the entire state
     *
     * @return Message
     */
    public function addProviderState(string $name, array $params, $overwrite = false): self
    {
        $providerState         = new \stdClass();
        $providerState->name   = $name;
        $providerState->params = $params;

        if ($overwrite === true) {
            $this->providerStates = [];
        }

        $this->providerStates[] = $providerState;

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

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $out                = [];
        $out['description'] = $this->getDescription();

        if (\count($this->providerStates) > 0) {
            $out['providerStates'] = $this->getProviderStates();
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
