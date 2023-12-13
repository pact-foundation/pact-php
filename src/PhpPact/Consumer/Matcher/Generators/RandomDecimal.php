<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random big decimal value with the provided number of digits
 */
class RandomDecimal implements GeneratorInterface
{
    public function __construct(private int $digits = 10)
    {
    }

    public function jsonSerialize(): object
    {
        return (object) [
            'digits'              => $this->digits,
            'pact:generator:type' => 'RandomDecimal',
        ];
    }
}
