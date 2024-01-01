<?php

namespace PhpPactTest\Xml;

use PhpPact\Consumer\Matcher\Formatters\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PHPUnit\Framework\TestCase;
use PhpPact\Xml\XmlText;

class XmlTextTest extends TestCase
{
    /**
     * @testWith ["example text"]
     *           [1.23]
     *           [481]
     *           [false]
     *           [true]
     *           [null]
     */
    public function testJsonSerializePredefinedTypes(mixed $content): void
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
        $this->assertSame(
            json_encode([
                'content' => null,
                'matcher' => [
                    'pact:matcher:type' => 'integer',
                    'min' => 2,
                    'max' => 8,
                ],
                'pact:generator:type' => 'RandomInt'
            ]),
            json_encode($text)
        );
    }
}
