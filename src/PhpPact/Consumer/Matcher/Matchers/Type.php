<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * This executes a type based match against the values, that is, they are equal if they are the same type.
 */
class Type extends AbstractMatcher
{
    /**
     * @param object|array<mixed>|string|float|int|bool|null $value
     */
    public function __construct(private object|array|string|float|int|bool|null $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return object|array<mixed>|string|float|int|bool|null
     */
    protected function getValue(): object|array|string|float|int|bool|null
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'type';
    }
}
