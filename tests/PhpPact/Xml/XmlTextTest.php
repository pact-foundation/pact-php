<?php

namespace PhpPactTest\Xml;

use PHPUnit\Framework\TestCase;
use PhpPact\Xml\Model\Matcher\Generator;
use PhpPact\Xml\Model\Matcher\Matcher;
use PhpPact\Xml\XmlText;

class XmlTextTest extends TestCase
{
    private XmlText $text;

    public function setUp(): void
    {
        $this->text = new XmlText();
        $this->text->setContent('testing');
    }

    public function testGetMatcherArray(): void
    {
        $this->text->setMatcher(new Matcher(
            fn (Matcher $matcher) => $matcher->setType('include'),
            fn (Matcher $matcher) => $matcher->setOptions(['value' => "te"]),
        ));

        $this->assertSame(
            json_encode([
                'content' => 'testing',
                'matcher' => [
                    'pact:matcher:type' => 'include',
                    'value' => 'te',
                ]
            ]),
            json_encode($this->text->getArray())
        );
    }

    public function testGetGeneratorArray(): void
    {
        $this->text->setContent(7);
        $this->text->setMatcher(new Matcher(
            fn (Matcher $matcher) => $matcher->setType('integer'),
        ));
        $this->text->setGenerator(new Generator(
            fn (Generator $generator) => $generator->setType('RandomInt'),
            fn (Generator $generator) => $generator->setOptions(['min' => 2, 'max' => 8]),
        ));

        $this->assertSame(
            json_encode([
                'content' => 7,
                'matcher' => [
                    'pact:matcher:type' => 'integer',
                    'min' => 2,
                    'max' => 8,
                ],
                'pact:generator:type' => 'RandomInt'
            ]),
            json_encode($this->text->getArray())
        );
    }

    public function testGetBaseArray(): void
    {
        $this->assertSame(
            json_encode([
                'content' => 'testing',
            ]),
            json_encode($this->text->getArray())
        );
    }
}
