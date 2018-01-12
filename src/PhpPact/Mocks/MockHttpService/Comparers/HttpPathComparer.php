<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers;

class HttpPathComparer
{

    /**
     * @param $expected string
     * @param $actual string
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $result = new Comparers\ComparisonResult(sprintf("has path %s", (string)$expected));

        if ($expected != $actual) {
            $result->recordFailure(new Comparers\DiffComparisonFailure($expected, $actual));
        }

        return $result;
    }
}
