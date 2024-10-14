<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Generator\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a URL with the mock server as the base URL.
 *
 * Example regex: .*(/path)$
 * Example example: http://localhost:1234/path
 */
class MockServerURL implements GeneratorInterface, JsonFormattableInterface
{
    public function __construct(private string $regex, private string $example)
    {
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:generator:type' => 'MockServerURL',
            'regex' => $this->regex,
            'example' => $this->example,
        ]);
    }
}
