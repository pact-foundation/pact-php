<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * This is the default matcher, and relies on the equals operator
 */
class Equality implements MatcherInterface
{
    /**
     * @param object|array<mixed>|string|float|int|bool|null $value
     */
    public function __construct(private object|array|string|float|int|bool|null $value)
    {
    }

    public function jsonSerialize(): object
    {
        return (object) [
            'pact:matcher:type' => $this->getType(),
            'value'             => $this->value
        ];
    }

    public function getType(): string
    {
        return 'equality';
    }
}
