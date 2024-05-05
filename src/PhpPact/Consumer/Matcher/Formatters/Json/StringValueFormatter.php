<?php

namespace PhpPact\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class StringValueFormatter extends HasGeneratorFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        if (!$matcher instanceof StringValue) {
            throw new MatcherNotSupportedException(sprintf('Matcher %s is not supported by %s', $matcher->getType(), self::class));
        }

        return [
            ...parent::format($matcher),
            'value' => $matcher->getValue(),
        ];
    }
}
