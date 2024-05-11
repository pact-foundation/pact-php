<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PHPUnit\Framework\TestCase;

class MinMaxTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $values = [
            1.23,
            2.34,
        ];
        $array = new MinMaxType($values, 2, 5);
        $this->assertSame(
            '{"pact:matcher:type":"type","min":2,"max":5,"value":[1.23,2.34]}',
            json_encode($array)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MinMaxType([], 0, 1);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MinMaxType([], 0, 1);
        $this->expectExceptionObject(new MatcherNotSupportedException("MinMaxType matcher doesn't support expression formatter"));
        $matcher->createExpressionFormatter();
    }
}
