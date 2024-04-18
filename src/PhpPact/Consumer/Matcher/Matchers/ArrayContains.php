<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\ValueRequiredFormatter;

/**
 * Checks if all the variants are present in an array.
 */
class ArrayContains extends AbstractMatcher
{
    /**
     * @param array<mixed> $variants
     */
    public function __construct(private array $variants)
    {
        $this->setFormatter(new ValueRequiredFormatter());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getAttributesData(): array
    {
        return ['variants' => $this->getValue()];
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return array_values($this->variants);
    }

    public function getType(): string
    {
        return 'arrayContains';
    }
}
