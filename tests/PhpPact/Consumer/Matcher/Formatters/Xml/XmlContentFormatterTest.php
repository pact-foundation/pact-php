<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Xml;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Xml\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class XmlContentFormatterTest extends TestCase
{
    #[TestWith([['key' => 'value'], 'array'])]
    #[TestWith([['key' => 'value'], 'object'])]
    public function testFormatInvalidValue(mixed $value, string $type): void
    {
        settype($value, $type);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Value of xml content must be string, float, int, bool or null');
        $matcher = new Type($value);
        $formatter = new XmlContentFormatter();
        $formatter->format($matcher);
    }

    #[TestWith([new RandomString(10), '{"content": "2001-01-02", "matcher": {"pact:matcher:type": "date", "format": "yyyy-MM-dd", "size": 10}, "pact:generator:type": "RandomString"}'])]
    #[TestWith([null, '{"content": "2001-01-02", "matcher": {"pact:matcher:type": "date", "format": "yyyy-MM-dd"}}'])]
    public function testFormatGenerator(?GeneratorInterface $generator, string $result): void
    {
        $matcher = new Date('yyyy-MM-dd', '2001-01-02');
        $matcher->setGenerator($generator);
        $formatter = new XmlContentFormatter();
        $jsonEncoded = json_encode($formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($result, $jsonEncoded);
    }

    #[TestWith(['text', '{"content": "text", "matcher": {"pact:matcher:type": "type"}}'])]
    #[TestWith([-123.45, '{"content": -123.45, "matcher": {"pact:matcher:type": "type"}}'])]
    #[TestWith([-12, '{"content": -12, "matcher": {"pact:matcher:type": "type"}}'])]
    #[TestWith([true, '{"content": true, "matcher": {"pact:matcher:type": "type"}}'])]
    #[TestWith([false, '{"content": false, "matcher": {"pact:matcher:type": "type"}}'])]
    #[TestWith([null, '{"content": null, "matcher": {"pact:matcher:type": "type"}}'])]
    public function testFormatValue(mixed $value, string $result): void
    {
        $matcher = new Type($value);
        $formatter = new XmlContentFormatter();
        $jsonEncoded = json_encode($formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($result, $jsonEncoded);
    }
}
