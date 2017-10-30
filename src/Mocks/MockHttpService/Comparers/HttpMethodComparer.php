<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers;

class HttpMethodComparer
{

    /**
     * @param $expected string
     * @param $actual string
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $result = new Comparers\ComparisonResult(sprintf("has method %s", (string)$expected));
        if ($expected != $actual) {
            $failure = new Comparers\DiffComparisonFailure($expected, $actual);
            $result->recordFailure($failure);
        }

        return $result;
    }
}
