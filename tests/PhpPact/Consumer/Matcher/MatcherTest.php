<?php

namespace PhpPactTest\Consumer\Matcher;

use PhpPact\Consumer\Matcher\Exception\MatcherException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\RegexFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
use PhpPact\Consumer\Matcher\Generators\Uuid;
use PhpPact\Consumer\Matcher\HttpStatus;
use PhpPact\Consumer\Matcher\Matcher;
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
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
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
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    private Matcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Matcher();
    }

    public function testSomethingLike(): void
    {
        $this->assertInstanceOf(Type::class, $this->matcher->somethingLike(123));
    }

    public function testLike(): void
    {
        $this->assertInstanceOf(Type::class, $this->matcher->like('abc'));
    }

    public function testEachLike(): void
    {
        $this->assertInstanceOf(MinType::class, $result = $this->matcher->eachLike('test'));
        $this->assertSame('test', $result->getValue());
        $this->assertSame(1, $result->getMin());
    }

    public function testAtLeastOneLike(): void
    {
        $this->assertInstanceOf(MinType::class, $result = $this->matcher->atLeastOneLike('test'));
        $this->assertSame('test', $result->getValue());
        $this->assertSame(1, $result->getMin());
    }

    public function testAtLeastLike(): void
    {
        $this->assertInstanceOf(MinType::class, $result = $this->matcher->atLeastLike('test', 2));
        $this->assertSame('test', $result->getValue());
        $this->assertSame(2, $result->getMin());
    }

    public function testAtMostLike(): void
    {
        $this->assertInstanceOf(MaxType::class, $result = $this->matcher->atMostLike('test', 2));
        $this->assertSame('test', $result->getValue());
        $this->assertSame(2, $result->getMax());
    }

    public function testConstrainedArrayLike(): void
    {
        $this->assertInstanceOf(MinMaxType::class, $result = $this->matcher->constrainedArrayLike('test', 2, 4));
        $this->assertSame('test', $result->getValue());
        $this->assertSame(2, $result->getMin());
        $this->assertSame(4, $result->getMax());
    }

    public function testTerm(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->term('123', '\d+'));
    }

    public function testRegex(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->regex('Games', 'Games|Other'));
    }

    public function testDateISO8601(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->dateISO8601('2010-01-17'));
    }

    #[TestWith(['T22:44:30.652Z'])]
    #[TestWith(['T22:44:30Z'])]
    #[TestWith(['T22:44Z'])]
    #[TestWith(['T22:44:30+01:00'])]
    #[TestWith(['T22:44:30+0100'])]
    #[TestWith(['T22:44:30+01'])]
    #[TestWith(['T22:44:30'])]
    #[TestWith(['T22:44:30-12:00'])]
    #[TestWith(['T22:44:30+0545'])]
    #[TestWith(['T22:44:30+14'])]
    public function testTimeISO8601(string $time): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->timeISO8601($time));
    }

    #[TestWith(['2015-08-06T16:53:10+01:00'])]
    #[TestWith(['2015-08-06T16:53:10+0100'])]
    #[TestWith(['2015-08-06T16:53:10+01'])]
    #[TestWith(['2015-08-06T16:53:10Z'])]
    #[TestWith(['2015-08-06T16:53:10'])]
    #[TestWith(['2015-08-06T16:53:10-12:00'])]
    #[TestWith(['2015-08-06T16:53:10+0545'])]
    #[TestWith(['2015-08-06T16:53:10+14'])]
    public function testDateTimeISO8601(string $dateTime): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->dateTimeISO8601($dateTime));
    }

    #[TestWith(['2015-08-06T16:53:10.123+01:00'])]
    #[TestWith(['2015-08-06T16:53:10.123+0100'])]
    #[TestWith(['2015-08-06T16:53:10.123+01'])]
    #[TestWith(['2015-08-06T16:53:10.123Z'])]
    #[TestWith(['2015-08-06T16:53:10.123'])]
    #[TestWith(['2015-08-06T16:53:10.123-12:00'])]
    #[TestWith(['2015-08-06T16:53:10.123+0545'])]
    #[TestWith(['2015-08-06T16:53:10.123+14'])]
    public function testDateTimeWithMillisISO8601(string $dateTime): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->dateTimeWithMillisISO8601($dateTime));
    }

    public function testTimestampRFC3339(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->timestampRFC3339('Mon, 31 Oct 2016 15:21:41 -0400'));
    }

    public function testInteger(): void
    {
        $this->assertInstanceOf(Type::class, $this->matcher->integer());
    }

    public function testBoolean(): void
    {
        $this->assertInstanceOf(Type::class, $this->matcher->boolean());
    }

    public function testDecimal(): void
    {
        $this->assertInstanceOf(Type::class, $this->matcher->decimal());
    }

    public function testIntegerV3(): void
    {
        $this->assertInstanceOf(Integer::class, $this->matcher->integerV3(13));
    }

    public function testBooleanV3(): void
    {
        $this->assertInstanceOf(Boolean::class, $this->matcher->booleanV3(true));
    }

    public function testDecimalV3(): void
    {
        $this->assertInstanceOf(Decimal::class, $this->matcher->decimalV3(13.01));
    }

    #[TestWith([null, true])]
    #[TestWith(['3F', false])]
    public function testHexadecimal(?string $value, bool $hasGenerator): void
    {
        $hexadecimal = $this->matcher->hexadecimal($value);
        $this->assertInstanceOf(Regex::class, $hexadecimal);
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomHexadecimal::class, $hexadecimal->getGenerator());
        } else {
            $this->assertNull($hexadecimal->getGenerator());
        }
    }

    #[TestWith([null, true])]
    #[TestWith(['ce118b6e-d8e1-11e7-9296-cec278b6b50a', false])]
    public function testUuid(?string $value, bool $hasGenerator): void
    {
        $uuid = $this->matcher->uuid($value);
        $this->assertInstanceOf(Regex::class, $uuid);
        if ($hasGenerator) {
            $this->assertInstanceOf(Uuid::class, $uuid->getGenerator());
        } else {
            $this->assertNull($uuid->getGenerator());
        }
    }

    public function testIpv4Address(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->ipv4Address());
    }

    public function testIpv6Address(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->ipv6Address());
    }

    public function testEmail(): void
    {
        $this->assertInstanceOf(Regex::class, $this->matcher->email());
    }

    public function testNullValue(): void
    {
        $this->assertInstanceOf(NullValue::class, $this->matcher->nullValue());
    }

    public function testDate(): void
    {
        $this->assertInstanceOf(Date::class, $this->matcher->date('yyyy-MM-dd', '2022-11-21'));
    }

    public function testTime(): void
    {
        $this->assertInstanceOf(Time::class, $this->matcher->time('HH:mm:ss', '21:45::31'));
    }

    public function testDateTime(): void
    {
        $this->assertInstanceOf(DateTime::class, $this->matcher->datetime("yyyy-MM-dd'T'HH:mm:ss", '2015-08-06T16:53:10'));
    }

    public function testString(): void
    {
        $this->assertInstanceOf(StringValue::class, $this->matcher->string('test string'));
    }

    public function testFromProviderState(): void
    {
        $uuid = $this->matcher->uuid();
        $this->assertInstanceOf(Regex::class, $uuid);
        $this->assertInstanceOf(Uuid::class, $uuid->getGenerator());
        $this->assertSame($uuid, $this->matcher->fromProviderState($uuid, '${id}'));
        $this->assertInstanceOf(ProviderState::class, $uuid->getGenerator());
    }

    public function testEqual(): void
    {
        $this->assertInstanceOf(Equality::class, $this->matcher->equal('test string'));
    }

    public function testIncludes(): void
    {
        $this->assertInstanceOf(Includes::class, $this->matcher->includes('test string'));
    }

    public function testNumber(): void
    {
        $this->assertInstanceOf(Number::class, $this->matcher->number(13.01));
    }

    public function testArrayContaining(): void
    {
        $this->assertInstanceOf(ArrayContains::class, $this->matcher->arrayContaining([
            'item 1',
            'item 2'
        ]));
    }

    public function testNotEmpty(): void
    {
        $this->assertInstanceOf(NotEmpty::class, $this->matcher->notEmpty('not empty string'));
    }

    public function testSemver(): void
    {
        $this->assertInstanceOf(Semver::class, $this->matcher->semver('1.2.3'));
    }

    public function testValidStatusCode(): void
    {
        $this->assertInstanceOf(StatusCode::class, $this->matcher->statusCode(HttpStatus::SUCCESS));
    }

    public function testValues(): void
    {
        $this->assertInstanceOf(Values::class, $this->matcher->values([
            'item 1',
            'item 2'
        ]));
    }

    public function testContentType(): void
    {
        $this->assertInstanceOf(ContentType::class, $this->matcher->contentType('image/jpeg'));
    }

    public function testEachKey(): void
    {
        $values = [
            'page 1' => 'Hello',
            'page 2' => 'World',
        ];
        $rules = [
            $this->matcher->regex('page 3', '^page \d+$'),
        ];
        $this->assertInstanceOf(EachKey::class, $this->matcher->eachKey($values, $rules));
    }

    public function testEachValue(): void
    {
        $values = [
            'vehicle 1' => 'car',
            'vehicle 2' => 'bike',
            'vehicle 3' => 'motorbike'
        ];
        $rules = [
            $this->matcher->regex('car', 'car|bike|motorbike'),
        ];
        $this->assertInstanceOf(EachValue::class, $this->matcher->eachValue($values, $rules));
    }

    #[TestWith([true, true])]
    #[TestWith([false, false])]
    public function testUrl(bool $useMockServerBasePath, bool $hasGenerator): void
    {
        $url = $this->matcher->url('http://localhost:1234/path', '.*(/path)$', $useMockServerBasePath);
        $this->assertInstanceOf(Regex::class, $url);
        if ($hasGenerator) {
            $this->assertInstanceOf(MockServerURL::class, $url->getGenerator());
        } else {
            $this->assertNull($url->getGenerator());
        }
    }

    public function testMatchingFieldWithNotSupportedFormat(): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage("MatchingField matcher doesn't support json formatter");
        $this->matcher->matchingField('address');
    }

    public function testMatchingField(): void
    {
        $matcher = new Matcher(plugin: true);
        $this->assertInstanceOf(MatchingField::class, $matcher->matchingField('address'));
    }

    public function testMatchAll(): void
    {
        $this->assertInstanceOf(MatchAll::class, $this->matcher->matchAll(['key' => 'value'], []));
    }

    public function testAtLeast(): void
    {
        $this->assertInstanceOf(MinType::class, $this->matcher->atLeast(123));
    }

    public function testAtMost(): void
    {
        $this->assertInstanceOf(MaxType::class, $this->matcher->atMost(123));
    }

    public function testWithFormatter(): void
    {
        $uuid = $this->matcher->uuid();
        $this->assertInstanceOf(HasGeneratorFormatter::class, $uuid->getFormatter());
        $matcher = new Matcher(plugin: true);
        $uuid = $matcher->uuid();
        $this->assertInstanceOf(RegexFormatter::class, $uuid->getFormatter());
    }
}
