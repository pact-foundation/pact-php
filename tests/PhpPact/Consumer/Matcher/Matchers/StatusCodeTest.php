<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StatusCodeTest extends TestCase
{
    #[TestWith([HttpStatus::INFORMATION, 123, null, '{"pact:matcher:type":"statusCode","status":"info","value":123}'])]
    #[TestWith([HttpStatus::SUCCESS, 123, null, '{"pact:matcher:type":"statusCode","status":"success","value":123}'])]
    #[TestWith([HttpStatus::REDIRECT, 123, null, '{"pact:matcher:type":"statusCode","status":"redirect","value":123}'])]
    #[TestWith([HttpStatus::CLIENT_ERROR, 123, null, '{"pact:matcher:type":"statusCode","status":"clientError","value":123}'])]
    #[TestWith([HttpStatus::SERVER_ERROR, 123, null, '{"pact:matcher:type":"statusCode","status":"serverError","value":123}'])]
    #[TestWith([HttpStatus::NON_ERROR, 123, null, '{"pact:matcher:type":"statusCode","status":"nonError","value":123}'])]
    #[TestWith([HttpStatus::ERROR, 123, null, '{"pact:matcher:type":"statusCode","status":"error","value":123}'])]
    #[TestWith([HttpStatus::INFORMATION, 0, new RandomInt(100, 199), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"info","min":100,"max":199,"value": 0}'])]
    #[TestWith([HttpStatus::SUCCESS, 0, new RandomInt(200, 299), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"success","min":200,"max":299,"value": 0}'])]
    #[TestWith([HttpStatus::REDIRECT, 0, new RandomInt(300, 399), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"redirect","min":300,"max":399,"value": 0}'])]
    #[TestWith([HttpStatus::CLIENT_ERROR, 0, new RandomInt(400, 499), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"clientError","min":400,"max":499,"value": 0}'])]
    #[TestWith([HttpStatus::SERVER_ERROR, 0, new RandomInt(500, 599), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"serverError","min":500,"max":599,"value": 0}'])]
    #[TestWith([HttpStatus::NON_ERROR, 0, new RandomInt(100, 399), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"nonError","min":100,"max":399,"value": 0}'])]
    #[TestWith([HttpStatus::ERROR, 0, new RandomInt(400, 599), '{"pact:matcher:type":"statusCode","pact:generator:type":"RandomInt","status":"error","min":400,"max":599,"value": 0}'])]
    public function testFormatJson(HttpStatus $status, int $value, ?GeneratorInterface $generator, string $json): void
    {
        $matcher = new StatusCode($status, $value);
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
