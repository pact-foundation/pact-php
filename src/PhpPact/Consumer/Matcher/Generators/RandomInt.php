<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random integer between the min and max values (inclusive)
 */
class RandomInt implements GeneratorInterface
{
    public function __construct(private int $min = 0, private int $max = 10)
    {
    }

    public function jsonSerialize(): object
    {
        return (object) [
            'min'                 => $this->min,
            'max'                 => $this->max,
            'pact:generator:type' => 'RandomInt',
        ];
    }
}
