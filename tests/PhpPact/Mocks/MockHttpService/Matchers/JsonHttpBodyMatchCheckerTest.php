<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use PHPUnit\Framework\TestCase;

class JsonHttpBodyMatchCheckerTest extends TestCase
{
    public function testMatch()
    {
        $matcher = new JsonHttpBodyMatchChecker(false);

        $body     = [];
        $body[]   = 'Test';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body   = [];
        $body[] = 'Test';
        $actual = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful match');

        // start vetting objects
        $body     = new \stdClass();
        $body->a1 = 'a1';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body     = new \stdClass();
        $body->a1 = 'a1';
        $actual   = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful match');

        // test editted objects
        $body     = new \stdClass();
        $body->a1 = 'a1';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body     = new \stdClass();
        $body->a1 = 'a2';
        $actual   = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), 'This should be a failed match - editing objects');

        // test new objects
        $body     = new \stdClass();
        $body->a1 = 'a1';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body     = new \stdClass();
        $body->a2 = 'a1';
        $actual   = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), 'This should be a failed match - removing objects');

        // test new objects (based on mapper allowKeysInObject = false
        $body     = new \stdClass();
        $body->a1 = 'a1';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body     = new \stdClass();
        $body->a1 = 'a1';
        $body->a2 = 'a2';
        $actual   = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), 'This should be a failed match - new objects not allowed in current config');

        // test new objects (based on mapper allowKeysInObject = false
        $matcher = new JsonHttpBodyMatchChecker(true);

        $body     = new \stdClass();
        $body->a1 = 'a1';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body     = new \stdClass();
        $body->a1 = 'a1';
        $body->a2 = 'a2';
        $actual   = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful match - new objects allowed in current config');
    }
}
