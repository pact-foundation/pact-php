<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\XmlElementFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Xml\XmlElement;
use PHPUnit\Framework\TestCase;

class XmlElementFormatterTest extends TestCase
{
    public function testFormatInvalidValue(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Value must be xml element');
        $matcher = new StringValue('example value');
        $matcher->setGenerator(new RandomString());
        $formatter = new XmlElementFormatter();
        $formatter->format($matcher);
    }

    /**
     * @testWith [null, {"pact:matcher:type": "type", "value": {"name": "test", "children": [], "attributes": []}}]
     *           [123,  {"pact:matcher:type": "type", "value": {"name": "test", "children": [], "attributes": []}, "examples": 123}]
     */
    public function testFormat(?int $examples, array $result): void
    {
        $value = new XmlElement(
            fn (XmlElement $element) => $element->setName('test'),
            fn (XmlElement $element) => $element->setExamples($examples),
        );
        $matcher = new Type($value);
        $formatter = new XmlElementFormatter();
        $this->assertSame(json_encode($result), json_encode($formatter->format($matcher)));
    }
}
