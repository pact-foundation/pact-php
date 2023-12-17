<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 */
class Type implements MatcherInterface
{
    /**
     * @param object|array<mixed>|string|float|int|bool|null $value
     */
    public function __construct(private object|array|string|float|int|bool|null $value)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:matcher:type' => $this->getType(),
            'value'             => $this->value,
        ];
    }

    public function getType(): string
    {
        return 'type';
    }
}
