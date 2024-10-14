<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random alphanumeric string of the provided length
 */
class RandomString implements GeneratorInterface, JsonFormattableInterface
{
    public function __construct(private int $size = 10)
    {
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'RandomString',
            'size' => $this->size,
        ]);
    }
}
