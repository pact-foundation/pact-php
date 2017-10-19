<?php

namespace Mocks\MockHttpService\Matchers;

use PHPUnit\Framework\TestCase;
use PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatchChecker;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;

class SerializeHttpBodyMatcherTest extends TestCase
{
    public function testMatch()
    {
        $matcher = new SerializeHttpBodyMatchChecker();

        $result = $matcher->Match("/", 'a', 'a');
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful match");

        $result = $matcher->Match("/", 'a', 'b');
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), "This should be a failed string match");

        // test json string
        $result = $matcher->Match("/", '{"id" : 1}', '{"id" : 1}');
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful json string match");

        // test arrays
        $a = array();
        $a[0] = "Zero";
        $a[1] = "One";

        $b = array();
        $b[0] = "Zero";
        $b[1] = "One";

        $result = $matcher->Match("/", $a, $b);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful array match");

        // test objects
        $a = new \stdClass();
        $a->Zero = 0;
        $a->One = array(1, 2);

        $b = new \stdClass();
        $b->Zero = 0;
        $b->One = array(1, 2);

        $result = $matcher->Match("/", $a, $b);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), "This should be a successful object match");

        // test failed objects
        $a = new \stdClass();
        $a->Zero = 0;
        $a->One = array(1, 2);

        $b = new \stdClass();
        $b->One = array(1, 3);

        $result = $matcher->Match("/", $a, $b);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), "This should be a failed object match");
    }
}
