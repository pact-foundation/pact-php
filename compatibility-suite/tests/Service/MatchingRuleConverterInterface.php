<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPactTest\CompatibilitySuite\Model\MatchingRule;

interface MatchingRuleConverterInterface
{
    public function convert(MatchingRule $rule, mixed $value): ?MatcherInterface;
}
