<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random string from the provided regular expression
 */
class Regex implements GeneratorInterface, JsonFormattableInterface
{
    public function __construct(private string $regex)
    {
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'Regex',
            'regex' => $this->regex,
        ]);
    }
}
