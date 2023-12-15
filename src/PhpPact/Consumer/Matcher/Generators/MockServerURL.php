<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a URL with the mock server as the base URL.
 *
 * Example regex: .*(/path)$
 * Example example: http://localhost:1234/path
 */
class MockServerURL extends AbstractGenerator
{
    public function __construct(private string $regex, private string $example)
    {
    }

    public function getType(): string
    {
        return 'MockServerURL';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return [
            'regex' => $this->regex,
            'example' => $this->example,
        ];
    }
}
