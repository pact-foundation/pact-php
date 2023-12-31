<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Allows defining matching rules to apply to the values in a collection. For maps, delgates to the Values matcher.
 */
class EachValue extends AbstractMatcher
{
    /**
     * @param array<mixed>|object $value
     * @param MatcherInterface[]  $rules
     */
    public function __construct(private object|array $value, private array $rules)
    {
        parent::__construct();
    }

    /**
     * @return array<string, MatcherInterface[]>
     */
    protected function getAttributesData(): array
    {
        return ['rules' => array_map(fn (MatcherInterface $rule) => $rule, $this->rules)];
    }

    /**
     * @return array<mixed>|object
     */
    protected function getValue(): object|array
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'eachValue';
    }
}
