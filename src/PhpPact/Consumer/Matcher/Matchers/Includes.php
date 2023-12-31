<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * This checks if the string representation of a value contains the substring.
 */
class Includes extends AbstractMatcher
{
    public function __construct(private string $value)
    {
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'include';
    }
}
