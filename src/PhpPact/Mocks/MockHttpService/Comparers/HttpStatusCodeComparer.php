<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers;

class HttpStatusCodeComparer
{
    /**
     * @param $expected string
     * @param $actual string
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $result = new Comparers\ComparisonResult(\sprintf('has status code %s', (string) $expected));
        if ($expected != $actual) {
            $result->recordFailure(new Comparers\DiffComparisonFailure($expected, $actual));
        }

        return $result;
    }
}
