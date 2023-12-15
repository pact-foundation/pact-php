<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\RandomDecimal;

/**
 * This checks if the type of the value is a number with decimal places.
 */
class Decimal extends GeneratorAwareMatcher
{
    public function __construct(private ?float $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomDecimal());
        }
    }

    public function getType(): string
    {
        return 'decimal';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): ?float
    {
        return $this->value;
    }
}
