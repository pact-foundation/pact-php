<?php

namespace PhpPact\Consumer\Matcher\Model;

interface CombinedMatchersInterface extends MatcherInterface
{
    /**
     * @return MatcherInterface[]
     */
    public function getMatchers(): array;
}
