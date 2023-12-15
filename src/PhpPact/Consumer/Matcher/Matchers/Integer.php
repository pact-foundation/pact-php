<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\RandomInt;

/**
 * This checks if the type of the value is an integer.
 */
class Integer extends GeneratorAwareMatcher
{
    public function __construct(private ?int $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomInt());
        }
    }

    public function getType(): string
    {
        return 'integer';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): ?int
    {
        return $this->value;
    }
}
