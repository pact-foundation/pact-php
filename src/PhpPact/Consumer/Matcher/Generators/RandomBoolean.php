<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random boolean value
 */
class RandomBoolean implements GeneratorInterface, JsonFormattableInterface
{
    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'RandomBoolean',
        ]);
    }
}
