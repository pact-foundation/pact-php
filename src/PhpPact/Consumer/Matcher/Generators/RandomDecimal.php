<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a random big decimal value with the provided number of digits
 */
class RandomDecimal extends AbstractGenerator
{
    public function __construct(private int $digits = 10)
    {
    }

    public function getType(): string
    {
        return 'RandomDecimal';
    }

    /**
     * @return array<string, int>
     */
    protected function getAttributesData(): array
    {
        return [
            'digits' => $this->digits,
        ];
    }
}
