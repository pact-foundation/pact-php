<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers\DiffComparisonFailure;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Comparers\ComparisonResult;

class HttpBodyComparer
{

    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\IHttpMessage
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\IHttpMessage
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $bodyMatcherCheckers = $expected->getBodyMatchers();
        $matchingRules = $expected->getMatchingRules();

        $result = new ComparisonResult("has a body");

        if ($expected->shouldSerializeBody() && $expected->getBody() == null && $actual->getBody()) {
            $result->recordFailure(new DiffComparisonFailure($expected, $actual));
            return $result;
        }

        if ($expected->getBody() == null) {
            return $result;
        }

        // cycle through matching rules
        foreach ($bodyMatcherCheckers as $bodyMatcherCheckerKey => $bodyMatcherChecker) {

            /**
             * @var $bodyMatcherChecker \PhpPact\Matchers\Checkers\IMatchChecker
             */
            $results = $bodyMatcherChecker->match($bodyMatcherCheckerKey, $expected, $actual, $matchingRules);

            /**
             * @var $results \PhpPact\Matchers\Checkers\MatcherResult
             */
            $checks = $results->getMatcherChecks();
            foreach ($checks as $check) {
                if (($check instanceof FailedMatcherCheck)) {
                    $result->recordFailure(new DiffComparisonFailure($expected, $actual));
                }
            }
        }

        return $result;
    }

    /**
     * Wrapper function to decode an object to JSON
     * @param $obj
     * @return mixed
     */
    private function jsonDecode($obj)
    {
        $json = \json_decode($obj);
        if ($json !== null) {
            $obj = $json;
        }
        return $obj;
    }


}
