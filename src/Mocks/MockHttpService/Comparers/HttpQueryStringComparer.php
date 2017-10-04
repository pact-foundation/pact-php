<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PHPUnit\Runner\Exception;

class HttpQueryStringComparer
{
    public function Compare($expected, $actual)
    {
        if (!$expected && !$actual) {
            return new \PhpPact\Comparers\ComparisonResult("has no query strings");
        }

        $expectedQuery = $expected;
        if (filter_var($expected, FILTER_VALIDATE_URL)) {
            $expectedQuery = parse_url($expected, PHP_URL_QUERY);
        }
        $expectedQuery = urldecode($expectedQuery);

        $actualQuery = $actual;
        if (filter_var($actual, FILTER_VALIDATE_URL)) {
            $actualQuery = parse_url($actual, PHP_URL_QUERY);
        }
        $actualQuery = urldecode($actualQuery);

        $result = new \PhpPact\Comparers\ComparisonResult(sprintf("has query %s", ($expectedQuery ? $expectedQuery : "null")));

        if (!$expectedQuery && !$actualQuery) {
            return new \PhpPact\Comparers\ComparisonResult("has no query strings");
        }


        if (!$expectedQuery || !$actualQuery) {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expected, $actual));
            return $result;
        }

        $expectedQueryItems = $this->ConvertQueryToAssociativeArray($expectedQuery);
        $actualQueryItems = $this->ConvertQueryToAssociativeArray($actualQuery);

        if (count($expectedQueryItems) != count($actualQueryItems)) {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expectedQuery, $actualQuery));
            return $result;
        }

        $queryDiff = array_diff($expectedQueryItems, $actualQueryItems);
        if (count($queryDiff) > 0) {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expectedQuery, $actualQuery));
            return $result;
        }


        return $result;
    }

    /**
     * Turn x=1&y=2 into Array ( [x] => 1, [y] => 2 )
     *
     * @param $query
     * @return array
     */
    private function ConvertQueryToAssociativeArray($query)
    {
        if (!trim($query)) {
            return array();
        }

        $result = array();

        $equalCount = substr_count($query, "=");
        $ampCount   = substr_count($query,"&");

        if ($equalCount > $ampCount) {
            $newQuery = "";
            $expectAmp = false;

            for( $i = 0; $i < strlen($query); $i++ ) {
                if ($query[$i] == "&") {
                    $expectAmp = false;
                    $newQuery .= $query[$i];
                }
                else if ($query[$i] == "=") {
                    if (!$expectAmp) {
                        $expectAmp = true;
                        $newQuery .= $query[$i];
                    } else {
                        $newQuery .= urlencode($query[$i]);
                    }
                } else {
                    $newQuery .= $query[$i];
                }
            }
        }

        $chunks = array_chunk(preg_split('/(&|=)/', $newQuery), 2);
        $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));

        return $result;
    }
}