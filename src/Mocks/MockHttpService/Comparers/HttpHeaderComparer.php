<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

class HttpHeaderComparer
{

    /**
     * @param $expected array
     * @param $actual array
     *
     * @return \PhpPact\Comparers\ComparisonResult
     */
    public function Compare($expected, $actual)
    {
        $result = new \PhpPact\Comparers\ComparisonResult("includes headers");

        if ($actual == null) {

            $result->RecordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Actual Headers are null"));
            return $result;
        }

        $expectedArray = $this->ObjectToArray($expected);
        $expectedArray = $this->MakeArrayLowerCase($expectedArray);

        $actualArray = $this->ObjectToArray($actual);
        $actualArray = $this->MakeArrayLowerCase($actualArray);

        foreach ($expectedArray as $header_key => $header_value) {
            $headerResult = new \PhpPact\Comparers\ComparisonResult(sprintf("'%s' with value %s", $header_key, $header_value));


            if (isset($actualArray[$header_key])) {
                $actualValue = $actualArray[$header_key];

                // split out the header values as an array and compare
                $actualKeywords = preg_split("/[\,;]+/", $actualValue);
                $expectedKeywords = preg_split("/[\,;]+/", $header_value);


                if (count($actualKeywords) == count($expectedKeywords) && count($actualKeywords) > 0){
                    $actualKeywords = array_map('trim', $actualKeywords);
                    $expectedKeywords = array_map('trim', $expectedKeywords);

                    if (!$this->array_diff_order($expectedKeywords, $actualKeywords)) {
                        $failure = new \PhpPact\Comparers\DiffComparisonFailure($header_value, $actualValue);
                        $headerResult->RecordFailure($failure);
                    }
                }
                else if ($header_value != $actualValue) {
                    $failure = new \PhpPact\Comparers\DiffComparisonFailure($header_value, $actualValue);
                    $headerResult->RecordFailure($failure);
                }
            } else {
                $failure = new \PhpPact\Comparers\ErrorMessageComparisonFailure(sprintf("Header with key '%s', does not exist in actual", $header_key));
                $headerResult->RecordFailure($failure);
            }

            $result->AddChildResult($headerResult);
        }

        return $result;
    }

    private function MakeArrayLowerCase($from)
    {
        $new = array();
        foreach ($from as $key => $value) {
            if (is_array($value)) {
                $value = $this->MakeArrayLowerCase($value);
            }

            $new[strtolower($key)] = $value;
        }

        return $new;
    }

    public function ObjectToArray ($object)
    {
        if (!is_object($object) && !is_array($object))
            return $object;

        return array_map(array($this, 'ObjectToArray'), (array)$object);
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
    private function array_diff_order( $array1, $array2 )
    {
        while ((list($key1, $val1) = each($array1)) && (list($key2, $val2) = each($array2)) ) {
            if($key1 != $key2 || $val1 != $val2) {
                return false;
            }
        }
        return true;
    }
}