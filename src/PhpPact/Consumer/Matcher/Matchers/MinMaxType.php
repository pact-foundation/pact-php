<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 * In addition, if the values represent a collection, the length of the actual value is compared against the minimum and maximum.
 */
class MinMaxType implements MatcherInterface
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(
        private array $values,
        private int $min,
        private int $max,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:matcher:type' => $this->getType(),
            'min'               => $this->min,
            'max'               => $this->max,
            'value'             => array_values($this->values),
        ];
    }

    public function getType(): string
    {
        return 'type';
    }
}
