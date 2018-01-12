<?php

namespace Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatchChecker;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use PHPUnit\Framework\TestCase;

class SerializeHttpBodyMatchCheckerTest extends TestCase
{
    public function testMatch()
    {
        $matcher = new SerializeHttpBodyMatchChecker();

        $body     = 'a';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body   = 'a';
        $actual = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful match');

        $body     = 'a';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body   = 'b';
        $actual = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), 'This should be a failed string match');

        // test json string
        $body     = '{"id" : 1}';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body   = '{"id" : 1}';
        $actual = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful json string match');

        // test arrays
        $body     = [];
        $body[0]  = 'Zero';
        $body[1]  = 'One';
        $expected = new ProviderServiceResponse(200, [], $body);

        $body    = [];
        $body[0] = 'Zero';
        $body[1] = 'One';
        $actual  = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful array match');

        // test objects
        $body       = new \stdClass();
        $body->Zero = 0;
        $body->One  = [1, 2];
        $expected   = new ProviderServiceResponse(200, [], $body);

        $body       = new \stdClass();
        $body->Zero = 0;
        $body->One  = [1, 2];
        $actual     = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof SuccessfulMatcherCheck), 'This should be a successful object match');

        // test failed objects
        $body       = new \stdClass();
        $body->Zero = 0;
        $body->One  = [1, 2];
        $expected   = new ProviderServiceResponse(200, [], $body);

        $body       = new \stdClass();
        $body->Zero = 0;
        $body->One  = [1, 3];
        $actual     = new ProviderServiceResponse(200, [], $body);

        $result = $matcher->match('/', $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof FailedMatcherCheck), 'This should be a failed object match');
    }
}
