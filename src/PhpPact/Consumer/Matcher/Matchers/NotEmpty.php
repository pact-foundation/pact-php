<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * Value must be present and not empty (not null or the empty string)
 */
class NotEmpty extends AbstractMatcher
{
    /**
     * @param object|array<mixed>|string|float|int|bool $value
     */
    public function __construct(private object|array|string|float|int|bool $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return object|array<mixed>|string|float|int|bool
     */
    protected function getValue(): object|array|string|float|int|bool
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'notEmpty';
    }
}
