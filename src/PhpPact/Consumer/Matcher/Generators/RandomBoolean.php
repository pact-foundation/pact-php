<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random boolean value
 */
class RandomBoolean implements GeneratorInterface
{
    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:generator:type' => 'RandomBoolean',
        ];
    }
}
