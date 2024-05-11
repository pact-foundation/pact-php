<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class NullValueFormatter implements JsonFormatterInterface
{
    /**
     * @return array<string, string>
     */
    public function format(MatcherInterface $matcher): array
    {
        return [
            'pact:matcher:type' => 'null',
        ];
    }
}
