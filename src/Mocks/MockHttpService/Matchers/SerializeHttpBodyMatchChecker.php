<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;

class SerializeHttpBodyMatchChecker implements \PhpPact\Matchers\Checkers\IMatchChecker
{
    /**
     * @param $path
     * @param $expected
     * @param $actual
     * @param $matchingRules array[MatchingRules]
     *
     * @return MatcherResult
     */
    public function match($path, $expected, $actual, $matchingRules = array())
    {
        if ($actual != null && serialize($expected) == serialize($actual)) {
            return new MatcherResult(new SuccessfulMatcherCheck($path));
        }

        return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
    }
}
