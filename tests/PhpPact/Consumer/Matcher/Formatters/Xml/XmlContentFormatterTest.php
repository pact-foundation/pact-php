<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Xml;

use PhpPact\Consumer\Matcher\Formatters\Xml\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class XmlContentFormatterTest extends TestCase
{
    #[TestWith([true, '2001-01-02', '{"content": "2001-01-02", "matcher": {"pact:matcher:type": "date", "format": "yyyy-MM-dd", "size": 10}, "pact:generator:type": "RandomString"}'])]
    #[TestWith([false, '2002-02-03', '{"content": "2002-02-03", "matcher": {"pact:matcher:type": "date", "format": "yyyy-MM-dd"}}'])]
    public function testFormat(bool $hasGenerator, ?string $value, string $result): void
    {
        $matcher = new Date('yyyy-MM-dd', $value);
        $generator = $hasGenerator ? new RandomString(10) : null;
        $matcher->setGenerator($generator);
        $formatter = new XmlContentFormatter();
        $jsonEncoded = json_encode($formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($result, $jsonEncoded);
    }
}
