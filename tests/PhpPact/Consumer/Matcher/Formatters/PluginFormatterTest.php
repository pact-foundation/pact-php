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
use PHPUnit\Framework\Attributes\TestWith;
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

    #[TestWith([new EachKey(["doesn't matter"], [])])]
    #[TestWith([new EachValue(["doesn't matter"], [])])]
    #[TestWith([new EachKey(["doesn't matter"], [new Type(1), new Type(2)])])]
    #[TestWith([new EachValue(["doesn't matter"], [new Type(1), new Type(2), new Type(3)])])]
    public function testInvalidRules(EachKey|EachValue $matcher): void
    {
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Matcher '%s' only support 1 rule, %d provided", $matcher->getType(), count($matcher->getRules())));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Type(new \stdClass()), 'object'])]
    #[TestWith([new Type(['key' => 'value']), 'array'])]
    #[TestWith([new MinType(['Example value'], 1), 'array'])]
    #[TestWith([new MaxType(['Example value'], 2), 'array'])]
    #[TestWith([new MinMaxType(['Example value'], 1, 2), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Plugin formatter doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Values([1, 2, 3])])]
    #[TestWith([new ArrayContains([new Equality(1)])])]
    #[TestWith([new StatusCode('clientError', 405)])]
    public function testNotSupportedMatcher(MatcherInterface $matcher): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf("Matcher '%s' is not supported by plugin", $matcher->getType()));
        $this->formatter->format($matcher);
    }

    #[TestWith([new MatchingField('product'), '"matching($\'product\')"'])]
    #[TestWith([new NotEmpty('test'), '"notEmpty(\'test\')"'])]
    #[TestWith([new EachKey(["doesn't matter"], [new Regex('\$(\.\w+)+', '$.test.one')]), '"eachKey(matching(regex, \'\\\\$(\\\\.\\\\w+)+\', \'$.test.one\'))"'])]
    #[TestWith([new EachValue(["doesn't matter"], [new Type(100)]), '"eachValue(matching(type, 100))"'])]
    #[TestWith([new Equality('Example value'), '"matching(equalTo, \'Example value\')"'])]
    #[TestWith([new Type('Example value'), '"matching(type, \'Example value\')"'])]
    #[TestWith([new Number(100.09), '"matching(number, 100.09)"'])]
    #[TestWith([new Integer(100), '"matching(integer, 100)"'])]
    #[TestWith([new Decimal(100.01), '"matching(decimal, 100.01)"'])]
    #[TestWith([new Includes('testing'), '"matching(include, \'testing\')"'])]
    #[TestWith([new Boolean(true), '"matching(boolean, true)"'])]
    #[TestWith([new Semver('1.0.0'), '"matching(semver, \'1.0.0\')"'])]
    #[TestWith([new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', '2020-05-21 16:44:32+10:00'), '"matching(datetime, \'yyyy-MM-dd HH:mm:ssZZZZZ\', \'2020-05-21 16:44:32+10:00\')"'])]
    #[TestWith([new Date('yyyy-MM-dd', '2012-04-12'), '"matching(date, \'yyyy-MM-dd\', \'2012-04-12\')"'])]
    #[TestWith([new Time('HH:mm', '22:04'), '"matching(time, \'HH:mm\', \'22:04\')"'])]
    #[TestWith([new Regex('\\w{3}\\d+', 'abc123'), '"matching(regex, \'\\\\w{3}\\\\d+\', \'abc123\')"'])]
    #[TestWith([new ContentType('application/xml'), '"matching(contentType, \'application\/xml\', \'application\/xml\')"'])]
    #[TestWith([new NullValue(), '"matching(type, null)"'])]
    public function testFormat(MatcherInterface $matcher, string $json): void
    {
        $this->assertSame($json, json_encode($this->formatter->format($matcher)));
    }
}
