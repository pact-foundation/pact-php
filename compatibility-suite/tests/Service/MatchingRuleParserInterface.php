<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\MatchingRule;

interface MatchingRuleParserInterface
{
    /**
     * @return array<int, MatchingRule>
     */
    public function parse(string $fileName): array;
}
