<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random boolean value
 */
class RandomBoolean implements GeneratorInterface
{
    public function jsonSerialize(): object
    {
        return (object) [
            'pact:generator:type' => 'RandomBoolean',
        ];
    }
}
