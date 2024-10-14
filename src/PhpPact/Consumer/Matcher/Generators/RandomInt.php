<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random integer between the min and max values (inclusive)
 */
class RandomInt implements GeneratorInterface, JsonFormattableInterface
{
    public function __construct(private int $min = 0, private int $max = 10)
    {
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'RandomInt',
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }
}
