<?php

namespace PhpPact\Consumer\Matcher\Trait;

use PhpPact\Consumer\Matcher\Model\MatcherInterface;

trait MatchersTrait
{
    /**
     * @var MatcherInterface[]
     */
    private array $matchers = [];

    public function addMatcher(MatcherInterface $matcher): void
    {
        $this->matchers[] = $matcher;
    }

    /**
     * @return MatcherInterface[]
     */
    public function getMatchers(): array
    {
        return $this->matchers;
    }
}
