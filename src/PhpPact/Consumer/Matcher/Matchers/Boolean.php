<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\RandomBoolean;

/**
 * Match if the value is a boolean value (booleans and the string values `true` and `false`)
 */
class Boolean extends GeneratorAwareMatcher
{
    public function __construct(private ?bool $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new RandomBoolean());
        }
    }

    public function getType(): string
    {
        return 'boolean';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): ?bool
    {
        return $this->value;
    }
}
