<?php

namespace PhpPactTest\Xml;

use PHPUnit\Framework\TestCase;
use PhpPact\Xml\Model\Matcher\Generator;
use PhpPact\Xml\Model\Matcher\Matcher;
use PhpPact\Xml\XmlElement;

class XmlElementTest extends TestCase
{
    private XmlElement $element;

    public function setUp(): void
    {
        $this->element = new XmlElement();
        $this->element->setName('Child');
        $this->element->addAttribute('myAttr', 'attr-value');
    }

    public function testGetMatcherArray(): void
    {
        $this->element->setMatcher(new Matcher(
            fn (Matcher $matcher) => $matcher->setType('type'),
            fn (Matcher $matcher) => $matcher->setOptions(['examples' => 7]),
        ));

        $this->assertSame(
            json_encode([
                'value' => [
                    'name' => 'Child',
                    'children' => [],
                    'attributes' => [
                        'myAttr' => 'attr-value',
                    ],
                ],
                'pact:matcher:type' => 'type',
                'examples' => 7,
            ]),
            json_encode($this->element->getArray())
        );
    }

    public function testGetGeneratorArray(): void
    {
        $this->element->setMatcher(new Matcher(
            fn (Matcher $matcher) => $matcher->setType('type'),
            fn (Matcher $matcher) => $matcher->setOptions(['examples' => 7]),
        ));
        $this->element->setGenerator(new Generator(
            fn (Generator $generator) => $generator->setType('Uuid'),
            fn (Generator $generator) => $generator->setOptions(['format' => 'simple']),
        ));

        $this->assertSame(
            json_encode([
                'value' => [
                    'name' => 'Child',
                    'children' => [],
                    'attributes' => [
                        'myAttr' => 'attr-value',
                    ]
                ],
                'pact:matcher:type' => 'type',
                'examples' => 7,
                'pact:generator:type' => 'Uuid',
                'format' => 'simple',
            ]),
            json_encode($this->element->getArray())
        );
    }

    public function testGetBaseArray(): void
    {
        $this->assertSame(
            json_encode([
                'name' => 'Child',
                'children' => [],
                'attributes' => [
                    'myAttr' => 'attr-value',
                ]
            ]),
            json_encode($this->element->getArray())
        );
    }
}
