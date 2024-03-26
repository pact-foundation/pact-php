<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\RandomInt;

/**
 * This checks if the type of the value is a number.
 */
class Number extends GeneratorAwareMatcher
{
    public function __construct(private int|float|null $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomInt());
        }
        parent::__construct();
    }

    public function getType(): string
    {
        return 'number';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    public function getValue(): int|float|null
    {
        return $this->value;
    }
}
