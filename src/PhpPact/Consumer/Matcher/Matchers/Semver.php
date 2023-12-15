<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\Regex;

/**
 * Value must be valid based on the semver specification
 */
class Semver extends GeneratorAwareMatcher
{
    public function __construct(private ?string $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new Regex('\d+\.\d+\.\d+'));
        }
    }

    public function getType(): string
    {
        return 'semver';
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    protected function getValue(): ?string
    {
        return $this->value;
    }
}
