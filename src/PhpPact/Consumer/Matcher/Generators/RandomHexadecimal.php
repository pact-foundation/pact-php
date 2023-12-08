<?php

namespace PhpPact\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

/**
 * Generates a random hexadecimal value of the given number of digits
 */
class RandomHexadecimal implements GeneratorInterface
{
    public function __construct(private int $digits = 10)
    {
    }

    /**
     * @return array<string, string|int>
     */
    public function jsonSerialize(): array
    {
        return [
            'digits'              => $this->digits,
            'pact:generator:type' => 'RandomHexadecimal',
        ];
    }
}
