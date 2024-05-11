<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\IncludesFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PHPUnit\Framework\TestCase;

class IncludesTest extends TestCase
{
    public function testSerialize(): void
    {
        $string = new Includes('contains this string');
        $this->assertSame(
            '{"pact:matcher:type":"include","value":"contains this string"}',
            json_encode($string)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Includes('text');
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Includes('text');
        $this->assertInstanceOf(IncludesFormatter::class, $matcher->createExpressionFormatter());
    }
}
