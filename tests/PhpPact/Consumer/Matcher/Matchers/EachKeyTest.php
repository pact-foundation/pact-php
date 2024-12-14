<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;

class EachKeyTest extends TestCase
{
    public function testFormatJson(): void
    {
        $value = [
            'abc' => 123,
            'def' => 111,
            'ghi' => [
                'test' => 'value',
            ],
        ];
        $rules = [
            new Type('string'),
            new Regex('\w{3}'),
        ];
        $matcher = new EachKey($value, $rules);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            <<<JSON
            {
                "pact:matcher:type": "eachKey",
                "value": {
                    "abc": 123,
                    "def": 111,
                    "ghi": {
                        "test": "value"
                    }
                },
                "rules": [
                    {
                        "pact:matcher:type": "type",
                        "value": "string"
                    },
                    {
                        "pact:matcher:type": "regex",
                        "regex": "\\\\w{3}",
                        "value": ""
                    }
                ]
            }
            JSON,
            $jsonEncoded
        );
    }

    public function testMissingRules(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Rules should not be empty');
        new EachKey(['key' => 'value'], []);
    }

    public function testTooManyRules(): void
    {
        $matcher = (new EachKey(['key' => 'value'], [new Type(1), new Type(2)]))->withFormatter(new ExpressionFormatter());
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Matcher 'eachKey' only support 1 rule in expression, %d provided", 2));
        json_encode($matcher);
    }

    public function testInvalidRules(): void
    {
        $matcher = (new EachKey(['key' => 'value'], [new StatusCode(HttpStatus::INFORMATION)]))->withFormatter(new ExpressionFormatter());
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf("Rule '%s' must implement '%s' to be formatted as expression", StatusCode::class, ExpressionFormattableInterface::class));
        json_encode($matcher);
    }

    #[TestWith([new EachKey(['key' => 'value'], [new StringValue("contains single quote '")]), "\"eachKey(matching(type, 'contains single quote \\\''))\""])]
    #[TestWith([new EachKey(['key' => 'value'], [new Integer(123)]), '"eachKey(matching(integer, 123))"'])]
    #[TestWith([new EachKey(new stdClass(), [new Regex('\w+', 'example value')]), "\"eachKey(matching(regex, '\\\w+', 'example value'))\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
