<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MinMaxTypeFormatter implements JsonFormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        if (!$matcher instanceof MinMaxType) {
            throw new MatcherNotSupportedException(sprintf('Matcher %s is not supported by %s', $matcher->getType(), self::class));
        }
        $examples = max($matcher->getMin(), 1); // Min can be zero, but number of examples must be at least 1

        return [
            'pact:matcher:type' => $matcher->getType(),
            'min' => $matcher->getMin(),
            'max' => $matcher->getMax(),
            'value' => array_fill(0, $examples, $matcher->getValue()),
        ];
    }
}
