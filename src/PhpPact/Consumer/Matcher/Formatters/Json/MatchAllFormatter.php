<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Model\JsonFormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MatchAllFormatter implements JsonFormatterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        if (!$matcher instanceof MatchAll) {
            throw new MatcherNotSupportedException(sprintf('Matcher %s is not supported by %s', $matcher->getType(), self::class));
        }

        return [
            'pact:matcher:type' => $matcher->getMatchers(),
            'value' => $matcher->getValue(),
        ];
    }
}
