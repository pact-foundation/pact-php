<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Ruby Standalone Mock Server for PACT tests.
 * Class Message.
 */
class Message implements \JsonSerializable
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var array
     */
    private array $providerStates = [];

    /**
     * @var array
     */
    private array $metadata;

    /**
     * @var mixed
     */
    private mixed $contents;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = [];
        foreach ($metadata as $key => $value) {
            $this->setMetadataValue($key, $value);
        }

        return $this;
    }

    private function setMetadataValue(string $key, string $value): void
    {
        $this->metadata[$key] = $value;
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
     * @return $this
     */
    public function setContents(mixed $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
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
