<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

class HttpPathComparer
{

    /**
     * @param $expected string
     * @param $actual string
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual)
    {
        $result = new \PhpPact\Comparers\ComparisonResult(sprintf("has path %s", (string)$expected));

        if ($expected != $actual) {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expected, $actual));
        }

        return $result;
    }
}
