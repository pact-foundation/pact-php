<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class NoGeneratorFormatter implements JsonFormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        return [
            'pact:matcher:type' => $matcher->getType(),
            ...$matcher->getAttributes()->getData(),
            'value' => $matcher->getValue(),
        ];
    }
}
