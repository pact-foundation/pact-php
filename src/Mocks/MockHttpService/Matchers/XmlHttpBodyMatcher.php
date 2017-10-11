<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\MatcherResult;
use PhpPact\Matchers\FailedMatcherCheck;
use PhpPact\Matchers\MatcherCheckFailureType;
use PhpPact\Matchers\SuccessfulMatcherCheck;

class XmlHttpBodyMatcher implements \PhpPact\Matchers\IMatcher
{

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
     * @return \PhpPact\Matchers\MatcherResult
     * @throws \Exception
     */
    public function Match($path, $expected, $actual)
    {
        if (method_exists($expected, "getBody") && method_exists($actual, "getBody")) {
            $expected = $expected->getBody();
            $actual = $actual->getBody();
        }

        // if we expect a match and the expected body is not set, call that successful
        if (!$expected) {
            return new MatcherResult(new SuccessfulMatcherCheck($path));
        }

        $jsonResults = $this->JsonDiff($expected, $actual);


        $diffs = count($jsonResults['new']) + count($jsonResults['edited']) + count($jsonResults['removed']);

        if ($diffs > 0) {
            if (!$this->_allowExtraKeys) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            } else if ((count($jsonResults['new']) + count($jsonResults['edited'])) > 0) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            }
        } else {
            // now check XML order
            return $this->CheckXmlOrder($expected, $actual, $path);
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
    private function JsonDiff($expected, $actual)
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
    private function CheckXmlOrder($expected, $actual, $path)
    {
        $expectedDom = new \DOMDocument();
        $expectedDom->loadXML($expected);
        $expectedLeafs = $this->GetLeafs($expectedDom);

        $actualDom = new \DOMDocument();
        $actualDom->loadXML($actual);
        $actualLeafs = $this->GetLeafs($actualDom);


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
    private function GetLeafs(\DOMDocument $xml)
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
}
