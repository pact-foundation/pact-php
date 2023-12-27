<?php

namespace PhpPact\Xml\Model\Matcher;

class Generator
{
    private string $type;

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    public function __construct(callable ...$options)
    {
        array_walk($options, fn (callable $option) => $option($this));
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArray(): array
    {
        return ['pact:generator:type' => $this->type] + $this->options;
    }
}
