<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random hexadecimal value of the given number of digits
 */
class RandomHexadecimal implements GeneratorInterface, JsonFormattableInterface
{
    public function __construct(private int $digits = 10)
    {
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'RandomHexadecimal',
            'digits' => $this->digits,
        ]);
    }
}
