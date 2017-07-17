<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

class HttpMethodComparer
{

    /**
     * @param $expected string
     * @param $actual string
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual)
    {
        $result = new \PhpPact\Comparers\ComparisonResult(sprintf("has method %s", (string)$expected));
        if ($expected != $actual) {
            $failure = new \PhpPact\Comparers\DiffComparisonFailure($expected, $actual);
            $result->RecordFailure($failure);
        }

        return $result;
    }
}