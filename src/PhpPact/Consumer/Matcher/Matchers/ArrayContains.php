<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\MinimalFormatter;

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
        $this->setFormatter(new MinimalFormatter());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getAttributesData(): array
    {
        return ['variants' => array_values($this->variants)];
    }

    /**
     * @todo Change return type to `null`
     */
    public function getValue(): mixed
    {
        return null;
    }

    public function getType(): string
    {
        return 'arrayContains';
    }
}
