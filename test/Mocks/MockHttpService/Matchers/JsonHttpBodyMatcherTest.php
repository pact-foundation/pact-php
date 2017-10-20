<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PHPUnit\Framework\TestCase;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatchChecker;

class JsonHttpBodyMatcherTest extends TestCase
{
    public function testMatch()
    {
        $matcher = new JsonHttpBodyMatchChecker(false);

        $expected = array();
        $expected[] = "Test";

        $actual = array();
        $actual[] = "Test";

        $result = $matcher->match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful match");

        // invalid object tests
        $hasException = false;
        try {
            $expected = "str-exp";
            $actual = array();
            $actual[] = "Test";
            $matcher->match("/", $expected, $actual);
        } catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException, "Expect an exception when expected is a string");


        // if actual is not an object, get a failed to match check
        $result = $matcher->match("/", array(), "b");
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), "This should be a successful match");


        // start vetting objects
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a1";

        $result = $matcher->match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful match");

        // test editted objects
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a2";

        $result = $matcher->match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), "This should be a failed match - editing objects");

        // test new objects
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a2 = "a1";

        $result = $matcher->match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), "This should be a failed match - removing objects");

        // test new objects (based on mapper allowKeysInObject = false
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a1";
        $actual->a2 = "a2";

        $result = $matcher->match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), "This should be a failed match - new objects not allowed in current config");

        // test new objects (based on mapper allowKeysInObject = false
        $matcher = new JsonHttpBodyMatchChecker(true);
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a1";
        $actual->a2 = "a2";

        $result = $matcher->match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful match - new objects allowed in current config");
    }
}
