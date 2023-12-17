<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Value must be present and not empty (not null or the empty string)
 */
class NotEmpty implements MatcherInterface
{
    /**
     * @param object|array<mixed>|string|float|int|bool $value
     */
    public function __construct(private object|array|string|float|int|bool $value)
    {
    }

    /**
     * @return array<string, mixed>
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
        return 'notEmpty';
    }
}
