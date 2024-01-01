<?php

namespace PhpPactTest\Xml;

use PhpPact\Xml\XmlText;
use PHPUnit\Framework\TestCase;
use PhpPact\Xml\XmlElement;

class XmlElementTest extends TestCase
{
    private XmlElement $element;

    public function setUp(): void
    {
        $child = new XmlElement(
            fn (XmlElement $element) => $element->setName('Child'),
        );
        $this->element = new XmlElement(
            fn (XmlElement $element) => $element->setName('Parent'),
            fn (XmlElement $element) => $element->addAttribute('myAttr', 'attr-value'),
            fn (XmlElement $element) => $element->setExamples(7),
            fn (XmlElement $element) => $element->addChild($child),
        );
    }

    public function testJsonSerializeWithoutText(): void
    {
        $this->assertSame(
            json_encode([
                'name' => 'Parent',
                'children' => [
                    [
                        'name' => 'Child',
                        'children' => [],
                        'attributes' => [],
                    ],
                ],
                'attributes' => [
                    'myAttr' => 'attr-value',
                ],
                'examples' => 7,
            ]),
            json_encode($this->element)
        );
    }

    public function testJsonSerializeWithText(): void
    {
        $this->element->setText(new XmlText('Inner text'));

        $this->assertSame(
            json_encode([
                'name' => 'Parent',
                'children' => [
                    [
                        'name' => 'Child',
                        'children' => [],
                        'attributes' => [],
                    ],
                    [
                        'content' => 'Inner text',
                    ],
                ],
                'attributes' => [
                    'myAttr' => 'attr-value',
                ],
                'examples' => 7,
            ]),
            json_encode($this->element)
        );
    }
}
