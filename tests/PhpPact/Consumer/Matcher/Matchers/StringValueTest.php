<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    /**
     * @testWith [null,   "{\"pact:matcher:type\":\"type\",\"value\":\"some string\",\"pact:generator:type\":\"RandomString\",\"size\":10}"]
     *           ["test", "{\"pact:matcher:type\":\"type\",\"value\":\"test\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $matcher = new StringValue($value);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
