<?php

namespace PhpPact\Consumer\Model;

class ProviderState
{
    private string $name;

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

    public function setParams(array $params = []): void
    {
        foreach ($params as $key => $value) {
            $this->addParam($key, $value);
        }
    }

    public function addParam(string $key, string $value): void
    {
        $this->params[$key] = $value;
    }
}
