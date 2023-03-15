<?php

namespace PhpPact\Consumer\Model\Interaction;

trait QueryTrait
{
    /**
     * @var array<string, string[]>
     */
    private array $query = [];

    /**
     * @return array<string, string[]>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array<string, string|string[]> $query
     */
    public function setQuery(array $query): self
    {
        $this->query = [];
        foreach ($query as $key => $value) {
            $this->addQueryParameter($key, $value);
        }

        return $this;
    }

    /**
     * @param string|string[] $value
     */
    public function addQueryParameter(string $key, array|string $value): self
    {
        $this->query[$key] = [];
        if (is_array($value)) {
            array_walk($value, fn (string $value) => $this->addQueryParameterValue($key, $value));
        } else {
            $this->addQueryParameterValue($key, $value);
        }

        return $this;
    }

    private function addQueryParameterValue(string $key, string $value): void
    {
        $this->query[$key][] = $value;
    }
}
