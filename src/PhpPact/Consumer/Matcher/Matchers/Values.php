<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Match the values in a map, ignoring the keys
 */
class Values implements MatcherInterface
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(private array $values)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:matcher:type' => $this->getType(),
            'value'             => $this->values,
        ];
    }

    public function getType(): string
    {
        return 'values';
    }
}
