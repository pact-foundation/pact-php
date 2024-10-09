<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Matchers\Max;
use PhpPact\Consumer\Matcher\Matchers\Min;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MatchAllTest extends TestCase
{
    public function testNestedMatchAllMatchers(): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage("Nested 'matcherAll' matcher is not supported");
        new MatchAll([], [new MatchAll([], [new Type(123)])]);
    }

    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new Min(2)]), <<<JSON
        {
            "pact:matcher:type": [
                {
                    "min": 2,
                    "pact:matcher:type": "type",
                    "value": [
                        null,
                        null
                    ]
                }
            ],
            "value": {
                "abc": 1,
                "def": 234
            }
        }
        JSON
    ])]
    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new Min(1), new Max(2), new EachKey(["doesn't matter"], [new Regex('\w+', 'abc')]), new EachValue(["doesn't matter"], [new Type(100)])]), <<<JSON
        {
            "pact:matcher:type": [
                {
                    "min": 1,
                    "pact:matcher:type": "type",
                    "value": [
                        null
                    ]
                },
                {
                    "max": 2,
                    "pact:matcher:type": "type",
                    "value": [
                        null
                    ]
                },
                {
                    "pact:matcher:type": "eachKey",
                    "rules": [
                        {
                            "pact:matcher:type": "regex",
                            "regex": "\\\\w+",
                            "value": "abc"
                        }
                    ],
                    "value": [
                        "doesn't matter"
                    ]
                },
                {
                    "pact:matcher:type": "eachValue",
                    "rules": [
                        {
                            "pact:matcher:type": "type",
                            "value": 100
                        }
                    ],
                    "value": [
                        "doesn't matter"
                    ]
                }
            ],
            "value": {
                "abc": 1,
                "def": 234
            }
        }
        JSON
    ])]
    #[TestWith([new MatchAll(['key' => 123], [new Min(1), new Max(2), new EachKey([], [new Type('test')]), new EachValue([], [new Type(123)])]), <<<JSON
        {
            "pact:matcher:type": [
                {
                    "pact:matcher:type": "type",
                    "min": 1,
                    "value": [
                        null
                    ]
                },
                {
                    "pact:matcher:type": "type",
                    "max": 2,
                    "value": [
                        null
                    ]
                },
                {
                    "pact:matcher:type": "eachKey",
                    "rules": [
                        {
                        "pact:matcher:type": "type",
                        "value": "test"
                        }
                    ],
                    "value": []
                },
                {
                    "pact:matcher:type": "eachValue",
                    "rules": [
                        {
                        "pact:matcher:type": "type",
                        "value": 123
                        }
                    ],
                    "value": []
                }
            ],
            "value": {
                "key": 123
            }
        }
        JSON
    ])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            $json,
            $jsonEncoded
        );
    }

    public function testMissingMatchers(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Matchers should not be empty');
        new MatchAll(['value'], []);
    }

    #[TestWith([new MatchAll(['abc' => 'xyz'], [new EachKey(["doesn't matter"], [new StringValue("contains single quote '")]), new EachValue(["doesn't matter"], [new StringValue("contains single quote '")])]), "\"eachKey(matching(type, 'contains single quote \\\'')), eachValue(matching(type, 'contains single quote \\\''))\""])]
    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new Min(2)]), '"atLeast(2)"'])]
    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new Min(1), new Max(2), new EachKey(["doesn't matter"], [new Regex('\w+', 'abc')]), new EachValue(["doesn't matter"], [new Type(100)])]), "\"atLeast(1), atMost(2), eachKey(matching(regex, '\\\w+', 'abc')), eachValue(matching(type, 100))\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
