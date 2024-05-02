<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    public function testSerialize(): void
    {
        $matcher = new NotEmpty(['some text']);
        $this->assertSame(
            '{"pact:matcher:type":"notEmpty","value":["some text"]}',
            json_encode($matcher)
        );
    }

    public function testSerializeIntoExpression(): void
    {
        $matcher = new NotEmpty('some text');
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            "\"notEmpty('some text')\"",
            json_encode($matcher)
        );
    }
}
