<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Models\HttpMessageInterface;

class SerializeHttpBodyMatchChecker implements \PhpPact\Matchers\Checkers\MatchCheckerInterface
{
    /**
     * @param $path
     * @param $expected
     * @param $actual
     * @param $matchingRules array[MatchingRules]
     *
     * @return MatcherResult
     */
    public function match($path, $expected, $actual, $matchingRules = [])
    {
        if (!($expected instanceof HttpMessageInterface)) {
            throw new \Exception('Expected is not an instance of IHttpMessage: ' . \print_r($expected, true));
        }

        if (!($actual instanceof HttpMessageInterface)) {
            throw new \Exception('Actual is not an instance of IHttpMessage: ' . \print_r($actual, true));
        }

        $actualBody   = $actual->getBody();
        $expectedBody = $expected->getBody();

        if ($actualBody != null && \serialize($expectedBody) == \serialize($actualBody)) {
            return new MatcherResult(new SuccessfulMatcherCheck($path));
        }

        return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
    }
}
