<?php

namespace PhpPactTest\Xml;

use PhpPact\Xml\Exception\InvalidXmlElementException;
use PHPUnit\Framework\TestCase;
use PhpPact\Xml\XmlBuilder;

class XmlBuilderTest extends TestCase
{
    public function testJsonSerializeInvalidXmlElement(): void
    {
        $this->expectException(InvalidXmlElementException::class);
        $this->expectExceptionMessage("Xml element's name is required");

        $builder = new XmlBuilder('1.0', 'UTF-8');

        $builder
            ->root(
                $builder->name('Root'),
                $builder->add(),
            )
        ;

        json_encode($builder);
    }

    public function testJsonSerialize(): void
    {
        $builder = new XmlBuilder('1.0', 'UTF-8');

        $builder
            ->root(
                $builder->name('Root'),
                $builder->add(
                    $builder->name('First Child Second Element'),
                    $builder->contentLike('Example Test')
                ),
                $builder->add(
                    $builder->name('Second Parent'),
                    $builder->add(
                        $builder->name('Second child 1'),
                        $builder->attribute('myAttr', 'Attr Value')
                    ),
                    $builder->add(
                        $builder->name('Second child 2'),
                        $builder->content('Test')
                    ),
                    $builder->add(
                        $builder->name('Third Parent'),
                        $builder->eachLike(
                            $builder->name('Child')
                        )
                    ),
                ),
                $builder->add(
                    $builder->name('First Child Third Element'),
                ),
            )
        ;

        $expectedArray = [
            'version' => '1.0',
            'charset' => 'UTF-8',
            'root' => [
                'name' => 'Root',
                'children' => [
                    [
                        'name' => 'FirstChildSecondElement',
                        'children' => [
                            [
                                'content' => 'Example Test',
                                'matcher' => ['pact:matcher:type' => 'type'],
                            ],
                        ],
                        'attributes' => [],
                    ],
                    [
                        'name' => 'SecondParent',
                        'children' => [
                            [
                                'name' => 'Secondchild1',
                                'children' => [],
                                'attributes' => ['myAttr' => 'Attr Value'],
                            ],
                            [
                                'name' => 'Secondchild2',
                                'children' => [['content' => 'Test']],
                                'attributes' => [],
                            ],
                            [
                                'name' => 'ThirdParent',
                                'children' => [
                                    [
                                        'pact:matcher:type' => 'type',
                                        'value' => [
                                            'name' => 'Child',
                                            'children' => [],
                                            'attributes' => [],
                                        ],
                                    ],
                                ],
                                'attributes' => [],
                            ],
                        ],
                        'attributes' => [],
                    ],
                    [
                        'name' => 'FirstChildThirdElement',
                        'children' => [],
                        'attributes' => [],
                    ],
                ],
                'attributes' => [],
            ],
        ];

        $this->assertSame(json_encode($expectedArray), json_encode($builder));
    }
}
