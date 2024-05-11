<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Model\CombinedMatchersInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PhpPact\Consumer\Matcher\Trait\MatchersTrait;

abstract class CombinedMatchers extends AbstractMatcher implements CombinedMatchersInterface
{
    use MatchersTrait;

    /**
     * @param array<mixed>|object $value
     * @param MatcherInterface[] $matchers
     */
    public function __construct(private object|array $value, array $matchers)
    {
        foreach ($matchers as $matcher) {
            if ($matcher instanceof CombinedMatchersInterface) {
                throw new MatcherNotSupportedException('Nested combined matchers are not supported');
            }
            $this->addMatcher($matcher);
        }
        parent::__construct();
    }

    protected function getAttributesData(): array
    {
        return [];
    }

    /**
     * @return array<mixed>|object
     */
    public function getValue(): object|array
    {
        return $this->value;
    }
}
