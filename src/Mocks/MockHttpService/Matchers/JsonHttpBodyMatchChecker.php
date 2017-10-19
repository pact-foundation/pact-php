<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\IMatchChecker;
use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;

class JsonHttpBodyMatchChecker implements IMatchChecker
{
    const PATH = "$..*";

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
     * @return \PhpPact\Matchers\Checkers\MatcherResult
     * @throws \Exception
     */
    public function Match($path, $expected, $actual)
    {
        // empty string check
        if (!is_object($expected) && !is_array($expected)) {
            throw new \Exception("Failed to compare objects.   If you are not testing objects, try the SerializeHttpBodyMatchChecker.");
        }


        if ((!is_object($expected) && !is_array($expected))  && (is_object($actual) || is_array($actual))) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
        }

        if (!$actual || (!is_object($actual) && !is_array($actual))) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
        }

        $treewalker = new \TreeWalker(
            array(
            "debug" => false,                     //true => return the execution time, false => not
            "returntype" => "array")              //Returntype = ["obj","jsonstring","array"]
        );

        /*
         * According to Pact, arrays have to remain the same regardless of extra keys allowed flag.
         *
         * Objects can have extra nodes but not arrays.   Here, we find all sub arrays and ensure they are the same
         */
        if ($this->_allowExtraKeys == true ) {
            $actualBody = $actual;
            $expectedBody = $expected;

            if (method_exists($expected, "getBody") &&  method_exists($actual, "getBody")) {
                $actualBody = $actual->getBody();
                $expectedBody = $expected->getBody();
            }

            $arraysInActual = array();
            $this->FindArrays($actualBody, $arraysInActual);

            $arraysInExpected = array();
            $this->FindArrays($expectedBody, $arraysInExpected);

            if (count($arraysInActual) != count($arraysInExpected)) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            }

            for ($i = 0; $i<count($arraysInExpected); $i++) {
                $testExpected = $arraysInExpected[$i];
                $testActual = $arraysInActual[$i];
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

        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }

    private function ProcessResults($results, $path, $allowExtraObjectKeys = false)
    {
        if (count($results['new']) > 0) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
        } elseif (count($results['edited']) > 0) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
        } elseif (count($results['removed']) > 0 && $allowExtraObjectKeys == false) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
        }

        return true;
    }

    private function FindArrays($obj, &$result)
    {
        if (is_object($obj)) {
            foreach ($obj as $key => $value) {
                $this->FindArrays($value, $result);
            }
        } elseif (is_array($obj)) {
            $result[] = $obj;
        }
    }
}
