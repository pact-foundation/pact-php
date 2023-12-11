<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a URL with the mock server as the base URL.
 *
 * Example regex: .*(/path)$
 * Example example: http://localhost:1234/path
 */
class MockServerURL implements GeneratorInterface
{
    public function __construct(private string $regex, private string $example)
    {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'regex'               => $this->regex,
            'example'             => $this->example,
            'pact:generator:type' => 'MockServerURL',
        ];
    }
}
