<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random string from the provided regular expression
 */
class Regex implements GeneratorInterface
{
    public function __construct(private string $regex)
    {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'regex'               => $this->regex,
            'pact:generator:type' => 'Regex',
        ];
    }
}
