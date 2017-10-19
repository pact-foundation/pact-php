<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Comparers;

class HttpHeaderComparer
{

    /**
     * @param $expected array
     * @param $actual array
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function compare($expected, $actual)
    {
        $result = new Comparers\ComparisonResult("includes headers");

        if ($actual == null) {
            $result->recordFailure(new Comparers\ErrorMessageComparisonFailure("Actual Headers are null"));
            return $result;
        }

        $expectedArray = $this->objectToArray($expected);
        $expectedArray = $this->makeArrayLowerCase($expectedArray);

        $actualArray = $this->objectToArray($actual);
        $actualArray = $this->makeArrayLowerCase($actualArray);

        foreach ($expectedArray as $header_key => $header_value) {
            $headerResult = new Comparers\ComparisonResult(sprintf("'%s' with value %s", $header_key, $header_value));


            if (isset($actualArray[$header_key])) {
                $actualValue = $actualArray[$header_key];

                // split out the header values as an array and compare
                $actualKeywords = preg_split("/[\,;]+/", $actualValue);
                $expectedKeywords = preg_split("/[\,;]+/", $header_value);


                if (count($actualKeywords) == count($expectedKeywords) && count($actualKeywords) > 0) {
                    $actualKeywords = array_map('trim', $actualKeywords);
                    $expectedKeywords = array_map('trim', $expectedKeywords);

                    if (!$this->arrayDiffOrder($expectedKeywords, $actualKeywords)) {
                        $failure = new Comparers\DiffComparisonFailure($header_value, $actualValue);
                        $headerResult->recordFailure($failure);
                    }
                } elseif ($header_value != $actualValue) {
                    $failure = new Comparers\DiffComparisonFailure($header_value, $actualValue);
                    $headerResult->recordFailure($failure);
                }
            } else {
                $failure = new Comparers\ErrorMessageComparisonFailure(sprintf("Header with key '%s', does not exist in actual", $header_key));
                $headerResult->recordFailure($failure);
            }

            $result->addChildResult($headerResult);
        }

        return $result;
    }

    private function makeArrayLowerCase($from)
    {
        $new = array();
        foreach ($from as $key => $value) {
            if (is_array($value)) {
                $value = $this->makeArrayLowerCase($value);
            }

            $new[strtolower($key)] = $value;
        }

        return $new;
    }

    public function objectToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }

        return array_map(array($this, 'objectToArray'), (array)$object);
    }

    /**
     * We want to compare array values but in order.  Return true if they match
     *
     * @link https://stackoverflow.com/posts/17353683/revisions
     *
     * @param $array1
     * @param $array2
     * @return bool
     */
    private function arrayDiffOrder($array1, $array2)
    {
        while ((list($key1, $val1) = each($array1)) && (list($key2, $val2) = each($array2))) {
            if ($key1 != $key2 || $val1 != $val2) {
                return false;
            }
        }
        return true;
    }
}
