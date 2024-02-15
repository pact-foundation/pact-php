<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ValueRequiredFormatter extends ValueOptionalFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher): array
    {
        return [
            ...parent::format($matcher),
            'value' => $matcher->getValue()];
    }
}
