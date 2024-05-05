<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\ArrayContains;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class ArrayContainsTest extends TestCase
{
    public function testSerialize(): void
    {
        $variants = [
            new Type('string'),
            new Integer(),
        ];
        $array = new ArrayContains($variants);
        $this->assertSame(
            '{"pact:matcher:type":"arrayContains","variants":[{"pact:matcher:type":"type","value":"string"},{"pact:matcher:type":"integer","pact:generator:type":"RandomInt","min":0,"max":10}],"value":[{"pact:matcher:type":"type","value":"string"},{"pact:matcher:type":"integer","pact:generator:type":"RandomInt","min":0,"max":10}]}',
            json_encode($array)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new ArrayContains([]);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new ArrayContains([]);
        $this->expectExceptionObject(new MatcherNotSupportedException("ArrayContains matcher doesn't support expression formatter"));
        $matcher->createExpressionFormatter();
    }
}
