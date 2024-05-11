<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\EqualityFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\Equality;
use PHPUnit\Framework\TestCase;

class EqualityTest extends TestCase
{
    public function testSerialize(): void
    {
        $string = new Equality('exact this string');
        $this->assertSame(
            '{"pact:matcher:type":"equality","value":"exact this string"}',
            json_encode($string)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Equality(null);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Equality(null);
        $this->assertInstanceOf(EqualityFormatter::class, $matcher->createExpressionFormatter());
    }
}
