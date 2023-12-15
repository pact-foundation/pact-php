<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a random integer between the min and max values (inclusive)
 */
class RandomInt extends AbstractGenerator
{
    public function __construct(private int $min = 0, private int $max = 10)
    {
    }

    public function getType(): string
    {
        return 'RandomInt';
    }

    /**
     * @return array<string, int>
     */
    protected function getAttributesData(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
