<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random alphanumeric string of the provided length
 */
class RandomString implements GeneratorInterface
{
    public function __construct(private int $size = 10)
    {
    }

    /**
     * @return array<string, string|int>
     */
    public function jsonSerialize(): array
    {
        return [
            'size'                => $this->size,
            'pact:generator:type' => 'RandomString',
        ];
    }
}
