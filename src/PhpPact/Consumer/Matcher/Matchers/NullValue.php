<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Match if the value is a null value (this is content specific, for JSON will match a JSON null)
 */
class NullValue implements MatcherInterface
{
    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'pact:matcher:type' => $this->getType(),
        ];
    }

    public function getType(): string
    {
        return 'null';
    }
}
