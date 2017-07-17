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
        $actualArray = $this->ObjectToArray($actual);
        $actualArray = $this->MakeArrayLowerCase($actualArray);

        foreach ($expectedArray as $header_key => $header_value) {
            $headerResult = new \PhpPact\Comparers\ComparisonResult(sprintf("'%s' with value %s", $header_key, $header_value));


            if (isset($actualArray[$header_key])) {
                $actualValue = $actualArray[$header_key];
                // we may want to split this out and compare each array
                //$keywords = preg_split("/[\,;]+/", $actualValue[$header_key]);
                if ($header_value != $actualValue) {
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
            } else {
                $value = strtolower($value);
            }
            $new[$key] = $value;
        }

        return $new;
    }

    public function ObjectToArray ($object)
    {
        if (!is_object($object) && !is_array($object))
            return $object;

        return array_map(array($this, 'ObjectToArray'), (array)$object);
    }
}