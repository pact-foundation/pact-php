<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers;

class HttpStatusCodeComparer
{
    /**
     * @param $expected string
     * @param $actual string
     * @param $matchingRules array[MatchingRules]
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual, $matchingRules = array())
    {
        $result = new Comparers\ComparisonResult(sprintf("has status code %s", (string)$expected));
        if ($expected != $actual) {
            $result->recordFailure(new Comparers\DiffComparisonFailure($expected, $actual));
        }

        return $result;
    }
}
