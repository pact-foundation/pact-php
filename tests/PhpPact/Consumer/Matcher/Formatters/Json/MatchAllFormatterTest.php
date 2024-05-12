<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\MatchAllFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MatchAllFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new MatchAllFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new MinType(null, 2)]), <<<JSON
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
    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new MinType(null, 1), new MaxType(null, 2), new EachKey(["doesn't matter"], [new Regex('\w+', 'abc')]), new EachValue(["doesn't matter"], [new Type(100)])]), <<<JSON
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
    public function testFormat(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
