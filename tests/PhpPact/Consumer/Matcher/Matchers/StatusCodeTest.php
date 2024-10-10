<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StatusCodeTest extends TestCase
{
    #[TestWith(['invalid', null, null])]
    #[TestWith(['info', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"info","min":100,"max":199,"value": null}'])]
    #[TestWith(['success', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"success","min":200,"max":299,"value": null}'])]
    #[TestWith(['redirect', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"redirect","min":300,"max":399,"value": null}'])]
    #[TestWith(['clientError', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"clientError","min":400,"max":499,"value": null}'])]
    #[TestWith(['serverError', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"serverError","min":500,"max":599,"value": null}'])]
    #[TestWith(['nonError', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"nonError","min":100,"max":399,"value": null}'])]
    #[TestWith(['error', null, '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"error","min":400,"max":599,"value": null}'])]
    #[TestWith(['info', 123, '{"pact:matcher:type":"statusCode","status":"info","value":123}'])]
    public function testFormatJson(string $status, ?int $value, ?string $json): void
    {
        if (!$json) {
            $this->expectException(InvalidHttpStatusException::class);
            $this->expectExceptionMessage("Status 'invalid' is not supported. Supported status are: info, success, redirect, clientError, serverError, nonError, error");
        }
        $matcher = new StatusCode($status, $value);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        if ($json) {
            $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
        }
    }
}
