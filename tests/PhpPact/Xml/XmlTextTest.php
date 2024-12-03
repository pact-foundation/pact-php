<?php

namespace PhpPactTest\Xml;

use PhpPact\Consumer\Matcher\Formatters\Xml\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use PhpPact\Xml\XmlText;

class XmlTextTest extends TestCase
{
    #[TestWith(['example text'])]
    #[TestWith([1.23])]
    #[TestWith([481])]
    #[TestWith([false])]
    #[TestWith([true])]
    #[TestWith([null])]
    public function testJsonSerializePredefinedTypes(string|float|int|bool|null $content): void
    {
        $text = new XmlText($content);
        $this->assertSame(json_encode(['content' => $content]), json_encode($text));
    }

    public function testJsonSerializeMatcher(): void
    {
        $matcher = new Integer();
        $matcher->setGenerator(new RandomInt(2, 8));
        $matcher->setFormatter(new XmlContentFormatter());
        $text = new XmlText($matcher);
        $expected = json_encode([
            'content' => 13,
            'matcher' => [
                'pact:matcher:type' => 'integer',
                'min' => 2,
                'max' => 8,
            ],
            'pact:generator:type' => 'RandomInt'
        ]);
        $this->assertIsString($expected);
        $actual = json_encode($text);
        $this->assertIsString($actual);
        $this->assertJsonStringEqualsJsonString($expected, $actual);
    }
}
