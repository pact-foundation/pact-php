<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * This checks if the string representation of a value contains the substring.
 */
class Includes implements MatcherInterface
{
    public function __construct(private string $value)
    {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:matcher:type' => $this->getType(),
            'value'             => $this->value,
        ];
    }

    public function getType(): string
    {
        return 'include';
    }
}
