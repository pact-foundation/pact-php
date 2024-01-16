<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Exception\GeneratorNotRequiredException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\ArrayContains;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Matchers\DateTime;
use PhpPact\Consumer\Matcher\Matchers\Decimal;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\Equality;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\MatchingField;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Number;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PhpPact\Consumer\Matcher\Matchers\StatusCode;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Matchers\Time;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Matchers\Values;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\TestCase;

class PluginFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new PluginFormatter();
    }

    public function testFormatWithGenerator(): void
    {
        $this->expectException(GeneratorNotRequiredException::class);
        $this->expectExceptionMessage('Generator is not support in plugin');
        $matcher = new StringValue('example value');
        $matcher->setGenerator(new RandomString());
        $this->formatter->format($matcher);
    }

    /**
     * @dataProvider invalidRulesProvider
     */
    public function testInvalidRules(EachKey|EachValue $matcher): void
    {
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Matcher '%s' only support 1 rule, %d provided", $matcher->getType(), count($matcher->getRules())));
        $this->formatter->format($matcher);
    }

    public function invalidRulesProvider(): array
    {
        return [
            [new EachKey(["doesn't matter"], [])],
            [new EachValue(["doesn't matter"], [])],
            [new EachKey(["doesn't matter"], [new Type(1), new Type(2)])],
            [new EachValue(["doesn't matter"], [new Type(1), new Type(2), new Type(3)])],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Plugin formatter doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    public function invalidValueProvider(): array
    {
        return [
            [new Type((object)['key' => 'value']), 'object'],
            [new Type(['key' => 'value']), 'array'],
            [new MinType(['Example value'], 1), 'array'],
            [new MaxType(['Example value'], 2), 'array'],
            [new MinMaxType(['Example value'], 1, 2), 'array'],
        ];
    }

    /**
     * @dataProvider notSupportedMatcherProvider
     */
    public function testNotSupportedMatcher(MatcherInterface $matcher): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf("Matcher '%s' is not supported by plugin", $matcher->getType()));
        $this->formatter->format($matcher);
    }

    public function notSupportedMatcherProvider(): array
    {
        return [
            [new Values([1, 2, 3])],
            [new ArrayContains([new Equality(1)])],
            [new StatusCode('clientError', 405)],
        ];
    }

    /**
     * @dataProvider matcherProvider
     */
    public function testFormat(MatcherInterface $matcher, string $json): void
    {
        $this->assertSame($json, json_encode($this->formatter->format($matcher)));
    }

    public function matcherProvider(): array
    {
        return [
            [new MatchingField('product'), '"matching($\'product\')"'],
            [new NotEmpty('test'), '"notEmpty(\'test\')"'],
            [new EachKey(["doesn't matter"], [new Regex('\$(\.\w+)+', '$.test.one')]), '"eachKey(matching(regex, \'\\\\$(\\\\.\\\\w+)+\', \'$.test.one\'))"'],
            [new EachValue(["doesn't matter"], [new Type(100)]), '"eachValue(matching(type, 100))"'],
            [new Equality('Example value'), '"matching(equalTo, \'Example value\')"'],
            [new Type('Example value'), '"matching(type, \'Example value\')"'],
            [new Number(100.09), '"matching(number, 100.09)"'],
            [new Integer(100), '"matching(integer, 100)"'],
            [new Decimal(100.01), '"matching(decimal, 100.01)"'],
            [new Includes('testing'), '"matching(include, \'testing\')"'],
            [new Boolean(true), '"matching(boolean, true)"'],
            [new Semver('1.0.0'), '"matching(semver, \'1.0.0\')"'],
            [new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', '2020-05-21 16:44:32+10:00'), '"matching(datetime, \'yyyy-MM-dd HH:mm:ssZZZZZ\', \'2020-05-21 16:44:32+10:00\')"'],
            [new Date('yyyy-MM-dd', '2012-04-12'), '"matching(date, \'yyyy-MM-dd\', \'2012-04-12\')"'],
            [new Time('HH:mm', '22:04'), '"matching(time, \'HH:mm\', \'22:04\')"'],
            [new Regex('\\w{3}\\d+', 'abc123'), '"matching(regex, \'\\\\w{3}\\\\d+\', \'abc123\')"'],
            [new ContentType('application/xml'), '"matching(contentType, \'application\/xml\', \'application\/xml\')"'],
            [new NullValue(), '"matching(type, null)"'],
        ];
    }
}
