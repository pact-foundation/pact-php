<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MaxTypeFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PHPUnit\Framework\TestCase;

class MaxTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $values = [
            'string value',
        ];
        $array = new MaxType($values, 3);
        $this->assertSame(
            '{"pact:matcher:type":"type","max":3,"value":["string value"]}',
            json_encode($array)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MaxType([], 0);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MaxType([], 0);
        $this->assertInstanceOf(MaxTypeFormatter::class, $matcher->createExpressionFormatter());
    }
}
