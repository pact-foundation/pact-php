<?php

namespace PhpPact\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class ValueRequiredFormatter extends ValueOptionalFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(MatcherInterface $matcher, ?GeneratorInterface $generator, mixed $value): array
    {
        return parent::format($matcher, $generator, $value) + ['value' => $value];
    }
}
