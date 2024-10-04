<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidRegexException;
use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    private string $regex = '\d+';

    #[TestWith(['number', true])]
    #[TestWith(['integer', false])]
    public function testInvalidRegex(string $value, bool $isArray): void
    {
        $values = $isArray ? [$value] : $value;
        $this->expectException(InvalidRegexException::class);
        $value = is_array($values) ? $values[0] : $values;
        $this->expectExceptionMessage("The value '{$value}' doesn't match pattern '{$this->regex}'. Failed with error code 0.");
        new Regex($this->regex, $values);
    }

    /**
     * @param string|string[]|null $values
     */
    #[TestWith([null, '{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\d+","value":null}'])]
    #[TestWith(['12+', '{"pact:matcher:type":"regex","regex":"\\\\d+","value":"12+"}'])]
    #[TestWith([['12.3', '456'], '{"pact:matcher:type":"regex","regex":"\\\\d+","value":["12.3","456"]}'])]
    public function testFormatJson(string|array|null $values, string $json): void
    {
        $matcher = new Regex($this->regex, $values);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Regex('\w \d', ['key' => 'a 1']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Regex matching expression doesn't support value of type %s", $type));
        json_encode($matcher);
    }

    #[TestWith([new Regex("['\w]+", "contains single quote '"), "\"matching(regex, '[\\\\'\\\\w]+', 'contains single quote \\\\'')\""])]
    #[TestWith([new Regex('\w{3}\d+', 'abc123'), "\"matching(regex, '\\\w{3}\\\d+', 'abc123')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
