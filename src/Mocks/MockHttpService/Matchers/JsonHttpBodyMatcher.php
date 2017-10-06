<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PHPUnit\Runner\Exception;

class JsonHttpBodyMatcher implements \PhpPact\Matchers\IMatcher
{
    CONST PATH = "$..*";

    private $_allowExtraKeys;

    public function __construct($allowExtraKeysInObjects)
    {
        $this->_allowExtraKeys = $allowExtraKeysInObjects;
    }

    /**
     * Check if expected and actual are empty strings or JSON objects
     *
     * @param $path
     * @param $expected
     * @param $actual
     * @return \PhpPact\Matchers\MatcherResult
     * @throws \Exception
     */
    public function Match($path, $expected, $actual)
    {
        // empty string check
        if (!is_object($expected) && !is_array($expected)) {
            throw new \Exception("Failed to compare objects.   If you are not testing objects, try the SerializeHttpBodyMatcher.");
        }


        if ((!is_object($expected) && !is_array($expected))  && (is_object($actual) || is_array($actual))) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::ValueDoesNotMatch));
        }

        if (!$actual || (!is_object($actual) && !is_array($actual))) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::ValueDoesNotMatch));
        }

        $treewalker = new \TreeWalker(array(
            "debug" => false,                     //true => return the execution time, false => not
            "returntype" => "array")              //Returntype = ["obj","jsonstring","array"]
        );

        /*
         * According to Pact, arrays have to remain the same regardless of extra keys allowed flag.
         *
         * Objects can have extra nodes but not arrays.   Here, we find all sub arrays and ensure they are the same
         */
        if ( method_exists($expected, "getBody") &&
            method_exists($actual, "getBody") &&
            $this->_allowExtraKeys == true
        ) {
            $arraysInActual = array();
            $this->FindArrays($actual->getBody(), $arraysInActual);
            error_log('$arraysInActual: '.print_r($arraysInActual, true)) ;

            $arraysInExpected = array();
            $this->FindArrays($expected->getBody(), $arraysInExpected);
            error_log('$arraysInExpected: '.print_r($arraysInExpected, true)) ;

            if (count($arraysInActual) != count($arraysInExpected)) {
                return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::AdditionalPropertyInObject));
            }

            for($i = 0; $i<count($arraysInExpected); $i++) {
                $testExpected = array_pop($arraysInExpected);
                $testActual = array_pop($arraysInActual);
                $diffs = $treewalker->getdiff($testExpected, $testActual, false);
                $results = $this->ProcessResults($diffs, $path, false);
                if ($results !== true) {
                    return $results;
                }
            }
        }

        $diffs = $treewalker->getdiff($expected, $actual, false);
        $results = $this->ProcessResults($diffs, $path, $this->_allowExtraKeys);
        if ($results !== true) {
            return $results;
        }

        return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\SuccessfulMatcherCheck($path));
    }

    private function ProcessResults($results, $path, $allowExtraObjectKeys = false)
    {
        if (count($results['new']) > 0) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::AdditionalPropertyInObject));
        } else if (count($results['edited']) > 0) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::ValueDoesNotMatch));
        } else if (count($results['removed']) > 0 && $allowExtraObjectKeys == false) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::AdditionalPropertyInObject));
        }

        return true;
    }

    private function FindArrays($obj, &$result)
    {
        if (is_object($obj)) {
            foreach ($obj as $key => $value) {
                $this->FindArrays($value, $result);
            }
        } else if (is_array($obj)) {
            $result[] = $obj;
        }
    }
}