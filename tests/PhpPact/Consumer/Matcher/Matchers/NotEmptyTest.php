<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\NotEmptyFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    public function testSerialize(): void
    {
        $array = new NotEmpty(['some text']);
        $this->assertSame(
            '{"pact:matcher:type":"notEmpty","value":["some text"]}',
            json_encode($array)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new NotEmpty('test');
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new NotEmpty('test');
        $this->assertInstanceOf(NotEmptyFormatter::class, $matcher->createExpressionFormatter());
    }
}
