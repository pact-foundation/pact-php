<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;

class EachValueTest extends TestCase
{
    public function testFormatJson(): void
    {
        $value = [
            'ab1',
            'cd2',
            'ef9',
        ];
        $rules = [
            new Type('string'),
            new Regex('\w{2}\d'),
        ];
        $matcher = new EachValue($value, $rules);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            <<<JSON
            {
                "pact:matcher:type": "eachValue",
                "value": [
                    "ab1",
                    "cd2",
                    "ef9"
                ],
                "rules": [
                    {
                        "pact:matcher:type": "type",
                        "value": "string"
                    },
                    {
                        "pact:matcher:type": "regex",
                        "regex": "\\\\w{2}\\\\d",
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
        new EachValue(['value'], []);
    }

    public function testTooManyRules(): void
    {
        $matcher = (new EachValue(['value'], [new Includes('a'), new Includes('b')]))->withFormatter(new ExpressionFormatter());
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Matcher 'eachValue' only support 1 rule in expression, %d provided", 2));
        json_encode($matcher);
    }

    public function testInvalidRules(): void
    {
        $matcher = (new EachValue(['value'], [new StatusCode(HttpStatus::INFORMATION)]))->withFormatter(new ExpressionFormatter());
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf("Rule '%s' must implement '%s' to be formatted as expression", StatusCode::class, ExpressionFormattableInterface::class));
        json_encode($matcher);
    }

    #[TestWith([new EachValue(['value'], [new StringValue("contains single quote '")]), "\"eachValue(matching(type, 'contains single quote \\\''))\""])]
    #[TestWith([new EachValue(['value'], [new StringValue('example value')]), "\"eachValue(matching(type, 'example value'))\""])]
    #[TestWith([new EachValue(new stdClass(), [new Regex('\w \d', 'a 1')]), "\"eachValue(matching(regex, '\\\w \\\d', 'a 1'))\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
