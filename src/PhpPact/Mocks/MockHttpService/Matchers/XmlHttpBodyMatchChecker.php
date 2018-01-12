<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Matchers\Rules\MatchingRule;
use PhpPact\Mocks\MockHttpService\Models\IHttpMessage;
use Zend\Xml2Json\Xml2Json;

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
     * @throws \Exception
     *
     * @return \PhpPact\Matchers\Checkers\MatcherResult
     */
    public function match($path, $expected, $actual, $matchingRules = [])
    {
        if (!($expected instanceof IHttpMessage)) {
            throw new \Exception('Expected is not an instance of IHttpMessage: ' . \print_r($expected, true));
        }

        if (!($actual instanceof IHttpMessage)) {
            throw new \Exception('Actual is not an instance of IHttpMessage: ' . \print_r($actual, true));
        }

        $newExpected = clone $expected;
        $newActual   = clone $actual;

        // if we expect a match and the expected body is not set, call that successful
        if (!$expected->getBody()) {
            return new MatcherResult(new SuccessfulMatcherCheck($path));
        }

        // set expected body and actual body to JSON
        $jsonExpectedBody = \json_decode(Xml2Json::fromXml($newExpected->getBody(), false));
        $newExpected->setBody($jsonExpectedBody);

        $jsonActualBody = \json_decode(Xml2Json::fromXml($newActual->getBody(), false));
        $newActual->setBody($jsonActualBody);

        if ($this->shouldApplyMatchers($matchingRules)) {
            $matchers = $newExpected->getMatchingRules();
            foreach ($matchers as $jsonPath => $rule) {
                /*
                 * @var $rule MatchingRule
                 */
                if (\stripos($jsonPath, '.' . static::PATH_PREFIX) !== false) {
                    $jsonPath = $this->modifyPathForXmlAttributes($jsonPath);
                    $rule->setJsonPath($jsonPath);
                    $matchers[$jsonPath] = $rule;
                }
            }

            $newExpected->setMatchingRules($matchers);

            $jsonHttpBodyMatchChecker = new JsonHttpBodyMatchChecker($this->_allowExtraKeys);
            $results                  = $jsonHttpBodyMatchChecker->match($path, $newExpected, $newActual, $newExpected->getMatchingRules());

            /**
             * @var \PhpPact\Matchers\Checkers\MatcherResult
             */
            $checks            = $results->getMatcherChecks();
            $numOfJsonFailures = 0;
            $numOfJsonMatches  = 0;
            foreach ($checks as $check) {
                if (($check instanceof FailedMatcherCheck)) {
                    $numOfJsonFailures++;
                } else {
                    $numOfJsonMatches++;
                }
            }

            // do we need to do anything to numOfJsonMatches?

            $numOfXmlFailures = 0;
            $numOfXmlMatches  = 0;
            foreach ($expected->getMatchingRules() as $jsonPath => $rule) {
                $passedXmlProcessing = $this->processXml($expected, $actual, $jsonPath);
                if ($passedXmlProcessing) {
                    $numOfXmlMatches++;
                } else {
                    $numOfXmlFailures++;
                }
            }

            if ($numOfXmlFailures > 0 || ($numOfXmlFailures == 0 && $numOfJsonFailures > 0)) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            }
        } else {
            $jsonResults = $this->jsonDiff($expected->getBody(), $actual->getBody());
            $diffs       = \count($jsonResults['new']) + \count($jsonResults['edited']) + \count($jsonResults['removed']);

            if ($diffs > 0) {
                if (!$this->_allowExtraKeys) {
                    return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
                }
                if ((\count($jsonResults['new']) + \count($jsonResults['edited'])) > 0) {
                    return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
                }
            } else {
                // now check XML order
                return $this->checkXmlOrder($expected->getBody(), $actual->getBody(), $path);
            }
        }

        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }

    /**
     * @param $xmlStr
     * @param $jsonPath
     *
     * @return array
     */
    public function findEligibleNodesByJsonPath($xmlStr, $jsonPath)
    {
        $eligibleNodes = [];

        $domDocument = new \DOMDocument();
        $domDocument->loadXML($xmlStr);

        // find the last item in the path
        $tokens = \explode('.', $jsonPath);
        if (\count($tokens) > 0) {
            $numOfWildCards = 0;
            $token          = false;
            $tokens         = \array_reverse($tokens, true);
            foreach ($tokens as $tokenValue) {
                if ($tokenValue == '*') {
                    $numOfWildCards++;
                } else {
                    $token = $tokenValue;

                    break;
                }
            }

            $results = $this->extractEnumeration($token);
            $token   = $results['parsedToken'];

            if ($token == 'body') {
                $moreEligibleNodes = $this->extractEligibleNodes($domDocument, false, $results['enumeration'], $numOfWildCards);
                $eligibleNodes     = \array_merge($eligibleNodes, $moreEligibleNodes);
            } else {
                $moreEligibleNodes = $this->extractEligibleNodes($domDocument, $results['enumeration'], $results['enumeration'], $numOfWildCards);
                $eligibleNodes     = \array_merge($eligibleNodes, $moreEligibleNodes);
            }
        }

        return $eligibleNodes;
    }

    /**
     * To use Peekmo, we need to modify the JSON path
     * $.body.animals[*].alligator['@phoneNumber'] => $.body.animals[*].alligator.@attributes[*].phoneNumber
     *
     * @todo make private and unit test
     *
     * @param $xmlJsonPath
     *
     * @return string
     */
    public function modifyPathForXmlAttributes($xmlJsonPath)
    {

        // extract position of @ attribute reference
        if (\stripos($xmlJsonPath, "['@") !== false || \stripos($xmlJsonPath, '["@') !== false) {
            $startPos = \stripos($xmlJsonPath, "['@");
            if ($startPos === false) {
                $startPos = \stripos($xmlJsonPath, '["@');
            }

            $newXmlJsonPath = \substr($xmlJsonPath, 0, $startPos);

            // remove any [*] in the existing original XML JSON Path
            // this is based on the way Xml2Json converts JSON with attributes
            $newXmlJsonPath = \str_replace('[*]', '', $newXmlJsonPath);

            // append the attributes
            $newXmlJsonPath .= '[*].@attributes.';

            $remainingXmlJsonPath = \substr($xmlJsonPath, $startPos, \strlen($xmlJsonPath) - $startPos);
            $attributeName        = \substr($remainingXmlJsonPath, 3, \strlen($remainingXmlJsonPath) - 5);
            $newXmlJsonPath .= $attributeName;

            $xmlJsonPath = $newXmlJsonPath;
        }

        return $xmlJsonPath;
    }

    /**
     * Recursively walk to the necessary depths based on the wildcard count
     *
     * Based on link
     *
     * @link https://stackoverflow.com/a/21066385
     *
     * @param \DOMDocument $domElement
     * @param int          $depth
     * @param int          $predecessor_depth
     * @param bool         $maxDepth
     * @param mixed        $currentDepth
     *
     * @return array
     */
    public function retrieveNodesAtGivenDepth($domElement, $currentDepth, $maxDepth)
    {
        $return = [];

        if ($currentDepth == $maxDepth) {
            return [$domElement];
        }

        foreach ($domElement->childNodes as $domChild) {
            if ($domChild->nodeType == XML_ELEMENT_NODE) {
                $childResults = $this->retrieveNodesAtGivenDepth($domChild, $currentDepth + 1, $maxDepth);
                $return       = \array_merge($return, $childResults);
            }
        }

        return $return;
    }

    /**
     * @param $expected IHttpMessage
     * @param $actual IHttpMessage
     * @param $jsonPath
     *
     * @return bool
     */
    private function processXml($expected, $actual, $jsonPath)
    {
        $expectedXmlStr = $expected->getBody();
        $actualXmlStr   = $actual->getBody();

        $expectedEligibleNodes = $this->findEligibleNodesByJsonPath($expectedXmlStr, $jsonPath);
        $actualEligibleNodes   = $this->findEligibleNodesByJsonPath($actualXmlStr, $jsonPath);

        // cycle through expected types building a list of expectedEligibleTypes
        // check that actuals not only are the expected type but are not completely empty (no children and no attributes)

        $expectedTypes      = [];
        $expectedChildTypes = [];
        foreach ($expectedEligibleNodes as $parentNode) {
            foreach ($parentNode->childNodes as $childNode) {
                $type                 = $childNode->nodeName;
                $expectedTypes[$type] = true;

                /**
                 * @var \DOMNode
                 */
                if ($childNode->hasChildNodes()) {
                    foreach ($childNode->childNodes as $grandchild) {
                        $type                      = $grandchild->nodeName;
                        $expectedChildTypes[$type] = true;
                    }
                }
            }
        }

        foreach ($actualEligibleNodes as $parentNode) {
            // test if is in the same type
            foreach ($parentNode->childNodes as $childNode) {
                if (!isset($expectedTypes[$childNode->nodeName])) {
                    return false;
                }

                // test that it is not empty
                if (!$childNode->hasChildNodes() && !$childNode->hasAttributes() && $childNode->nodeValue == '') {
                    return false;
                }

                if ($childNode->hasChildNodes()) {
                    foreach ($childNode->childNodes as $grandchild) {
                        if (!isset($expectedChildTypes[$grandchild->nodeName])) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Run a JSON diff comparison for an initial baseline
     *
     * @param $expected
     * @param $actual
     *
     * @return mixed|string
     */
    private function jsonDiff($expected, $actual)
    {
        // now Simple XML
        $expectedXml = \simplexml_load_string($expected);
        $json        = \json_encode($expectedXml);
        $expectedObj = \json_decode($json);

        // now Simple XML
        $actualXml = \simplexml_load_string($actual);
        $json      = \json_encode($actualXml);
        $actualObj = \json_decode($json);

        $treewalker = new \TreeWalker(
            [
                'debug'      => false,                     //true => return the execution time, false => not
                'returntype' => 'array']              //Returntype = ["obj","jsonstring","array"]
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
     *
     * @throws \Exception
     *
     * @return MatcherResult
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
        if (\count($expectedLeafs) != \count($actualLeafs)) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
        }

        // walk through each to make sure they are equal
        $leafCount = \count($expectedLeafs);
        for ($i = 0; $i < $leafCount; $i++) {
            $expectedLeaf = \array_pop($expectedLeafs);
            $actualLeaf   = \array_pop($actualLeafs);

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
     *
     * @return array
     */
    private function getLeafNodes(\DOMDocument $xml)
    {
        $xpath = new \DOMXPath($xml);

        $leafs = [];

        foreach ($xpath->evaluate('//*') as $node) {
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

        return \array_keys($leafs);
    }

    /**
     * Test if we should apply matching rules to the body
     *
     * @param $matchingRules[MatchingRules]
     *
     * @return bool
     */
    private function shouldApplyMatchers($matchingRules)
    {
        if (\count($matchingRules) > 0) {
            foreach ($matchingRules as $jsonPath => $matchingRule) {
                if (\stripos($jsonPath, '.' . static::PATH_PREFIX) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \DOMDocument $domDocument
     * @param $token string
     * @param bool|string $enumeration
     * @param mixed       $depth
     */
    private function extractEligibleNodes($domDocument, $token = false, $enumeration = false, $depth = false)
    {
        $eligibleNodes = [];
        if ($enumeration === false || $enumeration == '*') {
            $i = 0;
            while (\is_object($parentNode = $this->extractNode($domDocument, $token, $i, $depth))) {
                $eligibleNodes[] = $parentNode;
                $i++;
            }
        } else {
            if (\is_object($parentNode = $this->extractNode($domDocument, $token, $enumeration, $depth))) {
                $eligibleNodes[] = $parentNode;
            }
        }

        return $eligibleNodes;
    }

    /**
     * @param \DOMDocument $domDocument
     * @param bool|string  $token
     * @param bool|int     $enumeration
     * @param mixed        $maxDepth
     *
     * @return \DOMDocument|false
     */
    private function extractNode($domDocument, $token, $enumeration, $maxDepth)
    {
        $baseNode = false;
        if ($token === false) {
            $i = 0;
            foreach ($domDocument->childNodes as $childNode) {
                if ($i == $enumeration) {
                    $baseNode = $childNode;

                    break;
                }
            }
        } elseif (\is_numeric($enumeration)) {
            $baseNode = $domDocument->getElementsByTagName($token)->item($enumeration);
        }

        if (!($baseNode instanceof \DOMNode)) {
            //error_log("Based node is not a DOMNode : " . (is_object($baseNode)?get_class($baseNode):gettype($baseNode)));
            return false;
        }

        if ($maxDepth === 0) {
            $newDom   = new \DOMDocument();
            $baseNode = $newDom->importNode($baseNode, true);
            $newDom->appendChild($baseNode);

            if ($newDom->hasChildNodes()) {
                return $newDom;
            }

            return false;
        }

        $newDom = new \DOMDocument();

        $foundNodeArray = $this->retrieveNodesAtGivenDepth($baseNode, 0, $maxDepth);
        foreach ($foundNodeArray as $foundNode) {
            $foundNode = $newDom->importNode($foundNode, true);
            $newDom->appendChild($foundNode);
        }

        if ($newDom->hasChildNodes()) {
            return $newDom;
        }

        return false;
    }

    /**
     * Parse out token and enumeration from JSON Path
     *
     * @param $token
     *
     * @return array
     */
    private function extractEnumeration($token)
    {
        $results = [];

        if (\stripos($token, '[') === false) {
            $results['parsedToken'] = $token;
            $results['enumeration'] = false;

            return $results;
        }

        $results['parsedToken'] = \substr($token, 0, \stripos($token, '['));
        $length                 = \stripos($token, ']') - \stripos($token, '[');
        $results['enumeration'] = \substr($token, \stripos($token, '[') + 1, $length - 1);

        return $results;
    }
}
