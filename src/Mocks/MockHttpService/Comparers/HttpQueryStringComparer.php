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
        $expectedQuery = rtrim($expectedQuery, '&');

        $actualQuery = $actual;
        if (filter_var($actual, FILTER_VALIDATE_URL)) {
            $actualQuery = parse_url($actual, PHP_URL_QUERY);
        }
        $actualQuery = urldecode($actualQuery);

        $actualQuery = rtrim($actualQuery, '&');

        $result = new \PhpPact\Comparers\ComparisonResult(sprintf("has query %s", ($expectedQuery ? $expectedQuery : "null")));

        if (!$expectedQuery && !$actualQuery) {
            return new \PhpPact\Comparers\ComparisonResult("has no query strings");
        }

        if (!$expectedQuery || !$actualQuery) {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expected, $actual));
            return $result;
        }

        $expectedQueryItems = $this->ConvertQueryToArray($expectedQuery);
        $actualQueryItems = $this->ConvertQueryToArray($actualQuery);

        if (count($expectedQueryItems) != count($actualQueryItems)) {
            $result->RecordFailure(new \PhpPact\Comparers\DiffComparisonFailure($expectedQuery, $actualQuery));
            return $result;
        }

        if (!$this->CompareArray($expectedQueryItems, $actualQueryItems, $result)) {
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
    private function ConvertQueryToArray($query)
    {
        if (!trim($query)) {
            return array();
        }

        $equalCount = substr_count($query, "=");
        $ampCount   = substr_count($query, "&");

        if ($equalCount > $ampCount) {
            $newQuery = "";
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
    private function CompareArray($expected, $actual)
    {
        $expectedKeys = $this->ExplodeKeys($expected);
        $actualKeys = $this->ExplodeKeys($actual);

        // if the keys in the original strings do not equal, blow up
        if (count($expectedKeys) != count($actualKeys)) {
            return false;
        }

        // check that the unique keys are in the proper order
        /*
        $expectedKeyArr = array_keys($expectedKeys);
        $actualKeyArr = array_keys($actualKeys);
        $count = count($expectedKeyArr);
        for($i = 0; $i < $count; $i++) {
            if ($expectedKeyArr[$i] != $actualKeyArr[$i]) {
                return false;
            }
        }
        */

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
    private function ExplodeKeys($arr)
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
