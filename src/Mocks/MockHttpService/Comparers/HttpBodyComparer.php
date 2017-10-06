<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

class HttpBodyComparer
{

    /**
     * @param $expected \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param $actual \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     * @param $matchingRules array[IMatcher]
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual, $matchingRules)
    {
        $result = new \PhpPact\Comparers\ComparisonResult("has a body");


        if ($expected->ShouldSerializeBody() && $expected->getBody() == null && $actual->getBody())
        {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expected, $actual));
            return $result;
        }


        if ($expected->getBody() == null) {
            return $result;
        }

        // looking for an exact match at the object level
        if (is_string($expected)) {
            $expected = $this->JsonDecode($expected);
        }
        else if (method_exists($expected, "getBody") && is_string($expected->getBody())) {
            $expected = $this->JsonDecode($expected->getBody());
        }

        if (is_string($actual)) {
            $actual = $this->JsonDecode($actual);
        }
        else if (method_exists($actual, "getBody") && is_string($actual->getBody())) {
            $actual = $this->JsonDecode($actual->getBody());
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

    /**
     * Wrapper function to decode an object to JSON
     * @param $obj
     * @return mixed
     */
    private function JsonDecode($obj)
    {
        $json = \json_decode($obj);
        if ($json !== null) {
            $obj = $json;
        }
        return $obj;
    }
}