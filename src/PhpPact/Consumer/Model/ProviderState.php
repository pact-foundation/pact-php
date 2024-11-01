<?php

namespace PhpPact\Consumer\Model;

class ProviderState
{
    private string $name;

    /**
     * @var array<string, string>
     */
    private array $params = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string, string>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setParams(array $params = []): void
    {
        foreach ($params as $key => $value) {
            $this->addParam($key, is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR));
        }
    }

    public function addParam(string $key, string $value): void
    {
        $this->params[$key] = $value;
    }
}
