<?php

namespace PhpPactTest\Consumer\Matcher;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\JsonFormatter;
use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Generators\Regex as RegexGenerator;
use PhpPact\Consumer\Matcher\Generators\Date as DateGenerator;
use PhpPact\Consumer\Matcher\Generators\Time as TimeGenerator;
use PhpPact\Consumer\Matcher\Generators\DateTime as DateTimeGenerator;
use PhpPact\Consumer\Matcher\Generators\Uuid;
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
use PhpPact\Consumer\Matcher\Matchers\Max;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\Min;
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
        $this->assertInstanceOf(Type::class, $result = $this->matcher->somethingLike(123));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testLike(): void
    {
        $this->assertInstanceOf(Type::class, $result = $this->matcher->like('abc'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testEachLike(): void
    {
        $this->assertInstanceOf(MinType::class, $result = $this->matcher->eachLike('test'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testAtLeastOneLike(): void
    {
        $this->assertInstanceOf(MinType::class, $result = $this->matcher->atLeastOneLike('test'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testAtLeastLike(): void
    {
        $this->assertInstanceOf(MinType::class, $result = $this->matcher->atLeastLike('test', 2));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testAtMostLike(): void
    {
        $this->assertInstanceOf(MaxType::class, $result = $this->matcher->atMostLike('test', 2));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testConstrainedArrayLike(): void
    {
        $this->assertInstanceOf(MinMaxType::class, $result = $this->matcher->constrainedArrayLike('test', 2, 4));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    /**
     * @param string|string[]|null $values
     */
    #[TestWith([null, true])]
    #[TestWith(['123', false])]
    #[TestWith([['123', '234'], false])]
    public function testTerm(string|array|null $values, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->term($values, '\d+'));
        if ($hasGenerator) {
            $this->assertInstanceOf(RegexGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    /**
     * @param string|string[]|null $values
     */
    #[TestWith([null, true])]
    #[TestWith(['Games', false])]
    #[TestWith([['Games', 'Other'], false])]
    public function testRegex(string|array|null $values, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->regex($values, 'Games|Other'));
        if ($hasGenerator) {
            $this->assertInstanceOf(RegexGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testDateISO8601(): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->dateISO8601('2010-01-17'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
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
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->timeISO8601($time));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
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
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->dateTimeISO8601($dateTime));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
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
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->dateTimeWithMillisISO8601($dateTime));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testTimestampRFC3339(): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->timestampRFC3339('Mon, 31 Oct 2016 15:21:41 -0400'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith([123, false])]
    public function testInteger(?int $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Type::class, $result = $this->matcher->integer($value));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomInt::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
    }

    #[TestWith([null, true])]
    #[TestWith([true, false])]
    #[TestWith([false, false])]
    public function testBoolean(?bool $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Type::class, $result = $this->matcher->boolean($value));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomBoolean::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
    }

    #[TestWith([null, true])]
    #[TestWith([1.01, false])]
    public function testDecimal(?float $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Type::class, $result = $this->matcher->decimal($value));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomDecimal::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
    }

    #[TestWith([null, true])]
    #[TestWith([13, false])]
    public function testIntegerV3(?int $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Integer::class, $result = $this->matcher->integerV3($value));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomInt::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith([true, false])]
    #[TestWith([false, false])]
    public function testBooleanV3(?bool $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Boolean::class, $result = $this->matcher->booleanV3($value));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomBoolean::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith([13.01, false])]
    public function testDecimalV3(?float $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Decimal::class, $result = $this->matcher->decimalV3($value));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomDecimal::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
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
        $this->assertInstanceOf(JsonFormatter::class, $hexadecimal->getFormatter());
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
        $this->assertInstanceOf(JsonFormatter::class, $uuid->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['127.0.0.13', false])]
    public function testIpv4Address(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->ipv4Address($value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RegexGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['::ffff:192.0.2.128', false])]
    public function testIpv6Address(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->ipv6Address($value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RegexGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['hello@pact.io', false])]
    public function testEmail(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Regex::class, $result = $this->matcher->email($value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RegexGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testNullValue(): void
    {
        $this->assertInstanceOf(NullValue::class, $result = $this->matcher->nullValue());
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['2022-11-21', false])]
    public function testDate(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Date::class, $result = $this->matcher->date('yyyy-MM-dd', $value));
        if ($hasGenerator) {
            $this->assertInstanceOf(DateGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['21:45::31', false])]
    public function testTime(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Time::class, $result = $this->matcher->time('HH:mm:ss', $value));
        if ($hasGenerator) {
            $this->assertInstanceOf(TimeGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['2015-08-06T16:53:10', false])]
    public function testDateTime(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(DateTime::class, $result = $this->matcher->datetime("yyyy-MM-dd'T'HH:mm:ss", $value));
        if ($hasGenerator) {
            $this->assertInstanceOf(DateTimeGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['test string', false])]
    public function testString(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(StringValue::class, $result = $this->matcher->string($value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomString::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testFromProviderState(): void
    {
        $uuid = $this->matcher->uuid();
        $this->assertInstanceOf(Regex::class, $uuid);
        $this->assertInstanceOf(Uuid::class, $uuid->getGenerator());
        $this->assertNotSame($uuid, $result = $this->matcher->fromProviderState($uuid, '${id}'));
        $this->assertInstanceOf(ProviderState::class, $result->getGenerator());
    }

    public function testEqual(): void
    {
        $this->assertInstanceOf(Equality::class, $result = $this->matcher->equal('test string'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testIncludes(): void
    {
        $this->assertInstanceOf(Includes::class, $result = $this->matcher->includes('test string'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith([13, false])]
    #[TestWith([13.01, false])]
    public function testNumber(int|float|null $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Number::class, $result = $this->matcher->number($value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomInt::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testArrayContaining(): void
    {
        $this->assertInstanceOf(ArrayContains::class, $result = $this->matcher->arrayContaining([
            'item 1',
            'item 2'
        ]));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testNotEmpty(): void
    {
        $this->assertInstanceOf(NotEmpty::class, $result = $this->matcher->notEmpty('not empty string'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([null, true])]
    #[TestWith(['1.2.3', false])]
    public function testSemver(?string $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(Semver::class, $result = $this->matcher->semver($value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RegexGenerator::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith(['success', null, true])]
    #[TestWith(['success', 123, false])]
    #[TestWith([HttpStatus::SUCCESS, null, true])]
    #[TestWith([HttpStatus::SUCCESS, 123, false])]
    public function testValidStatusCode(string|HttpStatus $status, ?int $value, bool $hasGenerator): void
    {
        $this->assertInstanceOf(StatusCode::class, $result = $this->matcher->statusCode($status, $value));
        if ($hasGenerator) {
            $this->assertInstanceOf(RandomInt::class, $result->getGenerator());
        } else {
            $this->assertNull($result->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testInvalidStatusCode(): void
    {
        $this->expectException(InvalidHttpStatusException::class);
        $this->expectExceptionMessage("Status 'invalid' is not supported. Supported status are: info, success, redirect, clientError, serverError, nonError, error");
        $this->matcher->statusCode('invalid');
    }

    public function testValues(): void
    {
        $this->assertInstanceOf(Values::class, $result = $this->matcher->values([
            'item 1',
            'item 2'
        ]));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testContentType(): void
    {
        $this->assertInstanceOf(ContentType::class, $result = $this->matcher->contentType('image/jpeg'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
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
        $this->assertInstanceOf(EachKey::class, $result = $this->matcher->eachKey($values, $rules));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
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
        $this->assertInstanceOf(EachValue::class, $result = $this->matcher->eachValue($values, $rules));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    #[TestWith([true, true])]
    #[TestWith([false, false])]
    public function testUrl(bool $useMockServerBasePath, bool $hasGenerator): void
    {
        $url = $this->matcher->url('http://localhost:1234/path', '.*(\/path)$', $useMockServerBasePath);
        $this->assertInstanceOf(Regex::class, $url);
        if ($hasGenerator) {
            $this->assertInstanceOf(MockServerURL::class, $url->getGenerator());
        } else {
            $this->assertNull($url->getGenerator());
        }
        $this->assertInstanceOf(JsonFormatter::class, $url->getFormatter());
    }

    public function testMatchingField(): void
    {
        $this->assertInstanceOf(MatchingField::class, $result = $this->matcher->matchingField('address'));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testMatchAll(): void
    {
        $this->assertInstanceOf(MatchAll::class, $result = $this->matcher->matchAll(['key' => 'value'], [$this->matcher->like('text')]));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testAtLeast(): void
    {
        $this->assertInstanceOf(Min::class, $result = $this->matcher->atLeast(123));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testAtMost(): void
    {
        $this->assertInstanceOf(Max::class, $result = $this->matcher->atMost(123));
        $this->assertInstanceOf(JsonFormatter::class, $result->getFormatter());
    }

    public function testExpressionFormat(): void
    {
        $matcher = new Matcher(plugin: true);
        $this->assertInstanceOf(MatchingField::class, $result = $matcher->matchingField('address'));
        $this->assertInstanceOf(ExpressionFormatter::class, $result->getFormatter());
    }
}
