<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Formatters\XmlElementFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Date;
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
        $formatter = new XmlElementFormatter();
        $formatter->format(new StringValue(), new RandomString(), 'example value');
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
        $this->assertSame(json_encode($result), json_encode($formatter->format($matcher, null, $value)));
    }
}
