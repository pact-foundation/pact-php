<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\TypeFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $value = (object) ['key' => 'value'];
        $object = new Type($value);
        $this->assertSame(
            '{"pact:matcher:type":"type","value":{"key":"value"}}',
            json_encode($object)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Type('abc');
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Type('abc');
        $this->assertInstanceOf(TypeFormatter::class, $matcher->createExpressionFormatter());
    }
}
