<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;

class StatusCodeTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new StatusCode('info');
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new StatusCode('error', 404);
    }

    /**
     * @testWith ["invalid",     null,  null]
     *           ["info",        null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"info\",\"min\":100,\"max\":199}"]
     *           ["success",     null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"success\",\"min\":200,\"max\":299}"]
     *           ["redirect",    null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"redirect\",\"min\":300,\"max\":399}"]
     *           ["clientError", null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"clientError\",\"min\":400,\"max\":499}"]
     *           ["serverError", null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"serverError\",\"min\":500,\"max\":599}"]
     *           ["nonError",    null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"nonError\",\"min\":100,\"max\":399}"]
     *           ["error",       null,  "{\"pact:matcher:type\":\"statusCode\",\"pact:generator:type\":\"RandomInt\",\"status\":\"error\",\"min\":400,\"max\":599}"]
     *           ["info",        123,   "{\"pact:matcher:type\":\"statusCode\",\"status\":\"info\",\"value\":123}"]
     */
    public function testSerialize(string $status, ?int $value, ?string $json): void
    {
        if (!$json) {
            $this->expectException(InvalidHttpStatusException::class);
            $this->expectExceptionMessage("Status 'invalid' is not supported. Supported status are: info, success, redirect, clientError, serverError, nonError, error");
        }
        $matcher = new StatusCode($status, $value);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
