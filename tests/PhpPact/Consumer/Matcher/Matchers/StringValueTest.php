<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    protected StringValue $matcher;

    protected function setUp(): void
    {
        $this->matcher = new StringValue();
    }

    /**
     * @testWith [null,   "{\"pact:matcher:type\":\"type\",\"value\":\"some string\",\"pact:generator:type\":\"RandomString\",\"size\":10}"]
     *           ["test", "{\"pact:matcher:type\":\"type\",\"value\":\"test\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $this->matcher = new StringValue($value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
