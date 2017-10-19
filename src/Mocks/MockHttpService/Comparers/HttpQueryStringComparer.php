<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers\ComparisonResult;
use PhpPact\Comparers\DiffComparisonFailure;

class HttpQueryStringComparer
{
    public function compare($expected, $actual)
    {
        if (!$expected && !$actual) {
            return new ComparisonResult("has no query strings");
        }

        $expectedQuery = $expected;
        if (filter_var($expected, FILTER_VALIDATE_URL)) {
            $expectedQuery = parse_url($expected, PHP_URL_QUERY);
        }
        $expectedQuery = urldecode($expectedQuery);
        $expectedQuery = rtrim($expectedQuery, '&');

        $actualQuery = $actual;
        if (filter_var($actual, FILTER_VALIDATE_URL)) {
            $actualQuery = parse_url($actual, PHP_URL_QUERY);
        }
        $actualQuery = urldecode($actualQuery);

        $actualQuery = rtrim($actualQuery, '&');

        $result = new ComparisonResult(sprintf("has query %s", ($expectedQuery ? $expectedQuery : "null")));

        if (!$expectedQuery && !$actualQuery) {
            return new ComparisonResult("has no query strings");
        }

        if (!$expectedQuery || !$actualQuery) {
            $result->RecordFailure(new DiffComparisonFailure($expected, $actual));
            return $result;
        }

        $expectedQueryItems = $this->convertQueryToArray($expectedQuery);
        $actualQueryItems = $this->convertQueryToArray($actualQuery);

        if (count($expectedQueryItems) != count($actualQueryItems)) {
            $result->RecordFailure(new DiffComparisonFailure($expectedQuery, $actualQuery));
            return $result;
        }

        if (!$this->compareArray($expectedQueryItems, $actualQueryItems)) {
            $result->RecordFailure(new DiffComparisonFailure($expectedQuery, $actualQuery));
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
    private function convertQueryToArray($query)
    {
        if (!trim($query)) {
            return array();
        }

        $equalCount = substr_count($query, "=");
        $ampCount   = substr_count($query, "&");

        $newQuery = "";
        if ($equalCount > $ampCount) {
            $expectAmp = false;

            for ($i = 0; $i < strlen($query); $i++) {
                if ($query[$i] == "&") {
                    $expectAmp = false;
                    $newQuery .= $query[$i];
                } elseif ($query[$i] == "=") {
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

        $result = explode("&", trim($newQuery));

        return $result;
    }

    /**
     * Built a custom array sorter.   Keys can be reused but it they are, we need the array in the same order
     *
     * @param $expected array
     * @param $actual array
     *
     * @return bool
     */
    private function compareArray($expected, $actual)
    {
        $expectedKeys = $this->explodeKeys($expected);
        $actualKeys = $this->explodeKeys($actual);

        // if the keys in the original strings do not equal, blow up
        if (count($expectedKeys) != count($actualKeys)) {
            return false;
        }

        foreach ($expectedKeys as $expectedKey=>$expectedArr) {

            // if a key does not exist in expected and not actual, blow up
            if (!isset($actualKeys[$expectedKey])) {
                return false;
            }

            $actualArr = $actualKeys[$expectedKey];

            // if the array underneath this key are not the same, blow up
            if (count($expectedArr) != count($actualArr)) {
                return false;
            }

            // walk through each key subarray, if the values do not match, blow up
            $count = count($expectedArr);
            for ($i = 0; $i < $count; $i++) {
                if ($expectedArr[$i] != $actualArr[$i]) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Build an array where the keys are the first part of a key value pair followed by an array with the original entry
     *
     * Input
     *  Array (
     *      [0] = "animal=alligator"
     *      [1] = "hippo=Fred"
     *      [2] = "animal=hippo"
     *      [3] = "animal=elephant"
     *  )
     * Output
     *  Array (
     *      "animal" => Array(
     *              [0] = "animal=alligator"
     *              [1] = "animal=hippo"
     *              [2] = "animal=elephant"
     *          )
     *      "hippo" => Array (
     *              [0] = "hippo=Fred"
     *          )
     *      )
     * @param $arr
     * @return array
     */
    private function explodeKeys($arr)
    {
        $keys = array();

        foreach ($arr as $pair) {
            $subpair = explode("=", trim($pair), 2);
            if (!isset($keys[$subpair[0]])) {
                $keys[$subpair[0]] = array();
            }
            $keys[$subpair[0]][] = $pair;
        }

        return $keys;
    }
}
