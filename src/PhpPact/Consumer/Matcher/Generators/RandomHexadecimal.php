<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a random hexadecimal value of the given number of digits
 */
class RandomHexadecimal extends AbstractGenerator
{
    public function __construct(private int $digits = 10)
    {
    }

    public function getType(): string
    {
        return 'RandomHexadecimal';
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
