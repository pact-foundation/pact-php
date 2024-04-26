<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Formatters\ValueOptionalFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ValueOptionalFormatterTest extends TestCase
{
    #[TestWith([true, '2001-01-02', '{"pact:matcher:type": "date", "pact:generator:type": "RandomString", "format": "yyyy-MM-dd", "size": 10}'])]
    #[TestWith([false, '2002-02-03', '{"pact:matcher:type": "date", "format": "yyyy-MM-dd", "value": "2002-02-03"}'])]
    #[TestWith([true, null, '{"pact:matcher:type": "date", "pact:generator:type": "RandomString", "format": "yyyy-MM-dd", "size": 10}'])]
    #[TestWith([false, null, '{"pact:matcher:type": "date", "format": "yyyy-MM-dd", "value": null}'])]
    public function testFormat(bool $hasGenerator, ?string $value, string $result): void
    {
        $matcher = new Date('yyyy-MM-dd', $value);
        $generator = $hasGenerator ? new RandomString(10) : null;
        $matcher->setGenerator($generator);
        $formatter = new ValueOptionalFormatter();
        $jsonEncoded = json_encode($formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($result, $jsonEncoded);
    }
}
