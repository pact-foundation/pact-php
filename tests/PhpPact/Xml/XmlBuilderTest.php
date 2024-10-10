<?php

namespace PhpPactTest\Xml;

use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Xml\Exception\InvalidXmlElementException;
use PHPUnit\Framework\TestCase;
use PhpPact\Xml\XmlBuilder;

class XmlBuilderTest extends TestCase
{
    private XmlBuilder $builder;
    private Matcher $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new XmlBuilder('1.0', 'UTF-8');
        $this->matcher = new Matcher();
    }

    public function testJsonSerializeInvalidXmlElement(): void
    {
        $this->expectException(InvalidXmlElementException::class);
        $this->expectExceptionMessage("Xml element's name is required");

        $this->builder
            ->root(
                $this->builder->name('Root'),
                $this->builder->add(),
            )
        ;

        json_encode($this->builder);
    }

    public function testJsonSerialize(): void
    {
        $this->builder
            ->root(
                $this->builder->name('ns1:projects'),
                $this->builder->attribute('id', '1234'),
                $this->builder->attribute('xmlns:ns1', 'http://some.namespace/and/more/stuff'),
                $this->builder->content('List of projects'),
                $this->builder->eachLike(
                    $this->builder->examples(2),
                    $this->builder->name('ns1:project'),
                    $this->builder->attribute('id', $this->matcher->integerV3(1)),
                    $this->builder->attribute('type', 'activity'),
                    $this->builder->attribute('name', $this->matcher->string('Project 1')),
                    $this->builder->attribute('due', $this->matcher->datetime("yyyy-MM-dd'T'HH:mm:ss.SZ", '2016-02-11T09:46:56.023Z')),
                    $this->builder->contentLike('Project 1 description'),
                    $this->builder->add(
                        $this->builder->name('ns1:tasks'),
                        $this->builder->content('List of tasks'),
                        $this->builder->eachLike(
                            $this->builder->examples(5),
                            $this->builder->name('ns1:task'),
                            $this->builder->attribute('id', $this->matcher->integerV3(1)),
                            $this->builder->attribute('name', $this->matcher->string('Task 1')),
                            $this->builder->attribute('done', $this->matcher->boolean(true)),
                            $this->builder->contentLike('Task 1 description'),
                        ),
                    ),
                ),
            );

        $expectedArray = [
            'version' => '1.0',
            'charset' => 'UTF-8',
            'root' => [
                'name' => 'ns1:projects',
                'children' => [
                    [
                        'pact:matcher:type' => 'type',
                        'value' => [
                            'name' => 'ns1:project',
                            'children' => [
                                [
                                    'name' => 'ns1:tasks',
                                    'children' => [
                                        [
                                            'pact:matcher:type' => 'type',
                                            'value' => [
                                                'name' => 'ns1:task',
                                                'children' => [
                                                    [
                                                        'content' =>
                                                            'Task 1 description',
                                                        'matcher' => [
                                                            'pact:matcher:type' =>
                                                                'type',
                                                        ],
                                                    ],
                                                ],
                                                'attributes' => [
                                                    'id' => [
                                                        'pact:matcher:type' =>
                                                            'integer',
                                                        'value' => 1,
                                                    ],
                                                    'name' => [
                                                        'pact:matcher:type' => 'type',
                                                        'value' => 'Task 1',
                                                    ],
                                                    'done' => [
                                                        'pact:matcher:type' => 'type',
                                                        'value' => true,
                                                    ],
                                                ],
                                            ],
                                            'examples' => 5,
                                        ],
                                        [
                                            'content' => 'List of tasks',
                                        ],
                                    ],
                                    'attributes' => [],
                                ],
                                [
                                    'content' => 'Project 1 description',
                                    'matcher' => ['pact:matcher:type' => 'type'],
                                ],
                            ],
                            'attributes' => [
                                'id' => [
                                    'pact:matcher:type' => 'integer',
                                    'value' => 1,
                                ],
                                'type' => 'activity',
                                'name' => [
                                    'pact:matcher:type' => 'type',
                                    'value' => 'Project 1',
                                ],
                                'due' => [
                                    'pact:matcher:type' => 'datetime',
                                    'format' => "yyyy-MM-dd'T'HH:mm:ss.SZ",
                                    'value' => '2016-02-11T09:46:56.023Z',
                                ],
                            ],
                        ],
                        'examples' => 2,
                    ],
                    [
                        'content' => 'List of projects',
                    ],
                ],
                'attributes' => [
                    'id' => '1234',
                    'xmlns:ns1' => 'http://some.namespace/and/more/stuff',
                ],
            ],
        ];

        $expected = json_encode($expectedArray);
        $this->assertIsString($expected);
        $actual = json_encode($this->builder);
        $this->assertIsString($actual);
        $this->assertJsonStringEqualsJsonString($expected, $actual);
    }
}
