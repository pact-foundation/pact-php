<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Model\CombinedMatchersInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class CombinedMatchersFormatter extends ValueOptionalFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        if ($matcher instanceof CombinedMatchersInterface) {
            return [
                'pact:matcher:type' => $matcher->getMatchers(),
                'value' => $matcher->getValue(),
            ];
        }

        return parent::format($matcher);
    }
}
