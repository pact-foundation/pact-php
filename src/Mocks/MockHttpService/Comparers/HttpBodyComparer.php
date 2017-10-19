<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers\DiffComparisonFailure;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Comparers\ComparisonResult;

class HttpBodyComparer
{

    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param $matchingRules array[IMatcher]
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual, $matchingRules, $expectedContentType = "application/json")
    {
        $result = new ComparisonResult("has a body");


        if ($expected->ShouldSerializeBody() && $expected->getBody() == null && $actual->getBody()) {
            $result->RecordFailure(new DiffComparisonFailure($expected, $actual));
            return $result;
        }


        if ($expected->getBody() == null) {
            return $result;
        }

        // looking for an exact match at the object level
        if ($expectedContentType=="application/json") {
            if (is_string($expected)) {
                $expected = $this->jsonDecode($expected);
            } elseif (method_exists($expected, "getBody") && is_string($expected->getBody())) {
                $expected = $this->jsonDecode($expected->getBody());
            }

            if (is_string($actual)) {
                $actual = $this->jsonDecode($actual);
            } elseif (method_exists($actual, "getBody") && is_string($actual->getBody())) {
                $actual = $this->jsonDecode($actual->getBody());
            }
        }

        // cycle through matching rules
        foreach ($matchingRules as $matchingRuleKey => $matchingRule) {
            $results = $matchingRule->Match($matchingRuleKey, $expected, $actual);
            $checks = $results->getMatcherChecks();
            foreach ($checks as $check) {
                if (($check instanceof FailedMatcherCheck)) {
                    $result->RecordFailure(new DiffComparisonFailure($expected, $actual));
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
