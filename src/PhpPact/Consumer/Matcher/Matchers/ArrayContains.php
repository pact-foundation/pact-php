<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Checks if all the variants are present in an array.
 */
class ArrayContains implements MatcherInterface
{
    /**
     * @param array<mixed> $variants
     */
    public function __construct(private array $variants)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:matcher:type' => $this->getType(),
            'variants' => array_values($this->variants),
        ];
    }

    public function getType(): string
    {
        return 'arrayContains';
    }
}
