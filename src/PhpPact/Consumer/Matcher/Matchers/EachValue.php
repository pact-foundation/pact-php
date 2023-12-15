<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Allows defining matching rules to apply to the values in a collection. For maps, delgates to the Values matcher.
 */
class EachValue implements MatcherInterface
{
    /**
     * @param array<mixed>|object $value
     * @param MatcherInterface[]  $rules
     */
    public function __construct(private object|array $value, private array $rules)
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
            'rules'             => array_map(fn (MatcherInterface $rule) => $rule, $this->rules),
        ];
    }

    public function getType(): string
    {
        return 'eachValue';
    }
}
