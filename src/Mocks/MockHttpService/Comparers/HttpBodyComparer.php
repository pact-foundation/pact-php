<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PHPUnit\Runner\Exception;

class HttpBodyComparer
{

    /**
     * @param $expected string
     * @param $actual string
     * @param $matchingRules array[IMatcher]
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual, $matchingRules)
    {
        $result = new \PhpPact\Comparers\ComparisonResult("has a body");

        if (!$expected) {
            return $result;
        }

        if ($expected && !$actual) {
            $result->RecordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Actual Body is null"));
            return $result;
        }

        // looking for an exact match at the object level
        if (is_string($expected)) {
            $jsonExpected = \json_decode($expected);
            if ($jsonExpected !== null) {
                $expected = $jsonExpected;
            }

        }

        if (is_string($actual)) {
            $jsonActual = \json_decode($actual);
            if ($jsonActual !== null) {
                $actual = $jsonActual;
            }
        }

        // cycle through matching rules
        foreach($matchingRules as $matchingRuleKey => $matchingRule) {
            $results = $matchingRule->Match($matchingRuleKey, $expected, $actual);
            $checks = $results->getMatcherChecks();
            foreach($checks as $check)
            {
                if (($check instanceof \PhpPact\Matchers\FailedMatcherCheck)) {
                    $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expected, $actual));
                }
            }
        }



        return $result;
    }
}