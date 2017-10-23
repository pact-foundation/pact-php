<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Models\IHttpMessage;

class XmlHttpBodyMatchChecker implements \PhpPact\Matchers\Checkers\IMatchChecker
{

    const PATH_PREFIX = 'body';

    private $_allowExtraKeys;

    public function __construct($allowExtraKeysInObjects)
    {
        $this->_allowExtraKeys = $allowExtraKeysInObjects;
    }

    /**
     * Check if expected and actual are empty strings or XML objects
     *
     * @param $path
     * @param $expected
     * @param $actual
     * @param $matchingRules array[MatchingRules]
     *
     * @return \PhpPact\Matchers\Checkers\MatcherResult
     * @throws \Exception
     */
    public function match($path, $expected, $actual, $matchingRules = array())
    {
        if (!($expected instanceof IHttpMessage)) {
            throw new \Exception("Expected is not an instance of IHttpMessage: " . print_r($expected, true));
        }

        if (!($actual instanceof IHttpMessage)) {
            throw new \Exception("Actual is not an instance of IHttpMessage: " . print_r($actual, true));
        }

        $actualBody = $actual->getBody();
        $expectedBody = $expected->getBody();

        // if we expect a match and the expected body is not set, call that successful
        if (!$expectedBody) {
            return new MatcherResult(new SuccessfulMatcherCheck($path));
        }

        if ($this->shouldApplyMatchers($matchingRules)) {
            throw new \Exception("XML JSONPath not supported yet");
        }

        $jsonResults = $this->jsonDiff($expectedBody, $actualBody);
        $diffs = count($jsonResults['new']) + count($jsonResults['edited']) + count($jsonResults['removed']);

        if ($diffs > 0) {
            if (!$this->_allowExtraKeys) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            } else if ((count($jsonResults['new']) + count($jsonResults['edited'])) > 0) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            }
        } else {
            // now check XML order
            return $this->checkXmlOrder($expectedBody, $actualBody, $path);
        }

        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }

    /**
     * Run a JSON diff comparison for an initial baseline
     *
     * @param $expected
     * @param $actual
     * @return mixed|string
     */
    private function jsonDiff($expected, $actual)
    {
        // now Simple XML
        $expectedXml = simplexml_load_string($expected);
        $json = json_encode($expectedXml);
        $expectedObj = json_decode($json);

        // now Simple XML
        $actualXml = simplexml_load_string($actual);
        $json = json_encode($actualXml);
        $actualObj = json_decode($json);

        $treewalker = new \TreeWalker(
            array(
                "debug" => false,                     //true => return the execution time, false => not
                "returntype" => "array")              //Returntype = ["obj","jsonstring","array"]
        );

        $diffs = $treewalker->getdiff($expectedObj, $actualObj, false);

        return $diffs;
    }


    /**
     * Walk the XML paths and determine if expected and actual are in the same order
     *
     * @param $expected
     * @param $actual
     * @param $path
     * @return MatcherResult
     * @throws \Exception
     */
    private function checkXmlOrder($expected, $actual, $path)
    {
        $expectedDom = new \DOMDocument();
        $expectedDom->loadXML($expected);
        $expectedLeafs = $this->getLeafNodes($expectedDom);

        $actualDom = new \DOMDocument();
        $actualDom->loadXML($actual);
        $actualLeafs = $this->getLeafNodes($actualDom);


        // the path count did not match
        if (count($expectedLeafs) != count($actualLeafs)) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
        }

        // walk through each to make sure they are equal
        $leafCount = count($expectedLeafs);
        for ($i = 0; $i < $leafCount; $i++) {
            $expectedLeaf = array_pop($expectedLeafs);
            $actualLeaf = array_pop($actualLeafs);

            if ($expectedLeaf != $actualLeaf) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
            }
        }

        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }


    /**
     * Get all the leaf nodes and their path
     *
     * @param \DOMDocument $xml
     * @return array
     */
    private function getLeafNodes(\DOMDocument $xml)
    {
        $xpath = new \DOMXPath($xml);

        $leafs = array();

        foreach ($xpath->evaluate("//*") as $node) {
            $isPath = $xpath->evaluate('count(*) > 0', $node);
            $isLeaf = !$xpath->evaluate('count(*) > 0', $node);

            $path = '';
            foreach ($xpath->evaluate('ancestor::*', $node) as $parent) {
                $path .= '/' . $parent->nodeName;
            }

            $path .= '/' . ($node instanceof \DOMAttr ? '@' : '') . $node->nodeName;

            if ($isLeaf) {
                $leafs[$path] = true;
            }
        }

        return array_keys($leafs);
    }

    /**
     * Test if we should apply matching rules to the body
     *
     * @param $matchingRules[MatchingRules]
     *
     * @return bool
     */
    private function shouldApplyMatchers($matchingRules) {

        if (count($matchingRules) > 0) {
            foreach($matchingRules as $jsonPath => $matchingRule) {
                if (stripos($jsonPath, '.' . static::PATH_PREFIX) !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}
