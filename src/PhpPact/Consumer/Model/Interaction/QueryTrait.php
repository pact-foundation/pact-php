<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

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
     * @param MatcherInterface|MatcherInterface[]|string|string[] $value
     *
     * @throws JsonException
     */
    public function addQueryParameter(string $key, array|string|MatcherInterface $value): self
    {
        $this->query[$key] = [];
        if (is_array($value)) {
            array_walk($value, fn (string|MatcherInterface $value) => $this->addQueryParameterValue($key, $value));
        } else {
            $this->addQueryParameterValue($key, $value);
        }

        return $this;
    }

    /**
     * @throws JsonException
     */
    private function addQueryParameterValue(string $key, string|MatcherInterface $value): void
    {
        $this->query[$key][] = is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR);
    }
}
