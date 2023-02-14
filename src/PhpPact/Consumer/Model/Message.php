<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Ruby Standalone Mock Server for PACT tests.
 */
class Message implements \JsonSerializable
{
    private string $description;

    /**
     * @var array<int, \stdClass>
     */
    private array $providerStates = [];

    /**
     * @var array<string, string>
     */
    private array $metadata;

    /**
     * @var mixed
     */
    private $contents;

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
     * @return array<int, \stdClass>
     */
    public function getProviderStates(): array
    {
        return $this->providerStates;
    }

    /**
     * @param array<mixed, mixed> $params
     * @return array<int, \stdClass>
     */
    public function setProviderState(string $name, array $params = [], bool $overwrite = true): array
    {
        $this->addProviderState($name, $params, $overwrite);

        return $this->providerStates;
    }

    /**
     * @param string $name
     * @param array<mixed, mixed>  $params
     * @param bool   $overwrite - if true reset the entire state
     */
    public function addProviderState(string $name, array $params, bool $overwrite = false): self
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

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     */
    public function setContents($contents): self
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
