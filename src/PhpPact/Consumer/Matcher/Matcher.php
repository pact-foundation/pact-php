<?php

namespace PhpPact\Consumer\Matcher;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Exception\MatcherException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\JsonFormatter;
use PhpPact\Consumer\Matcher\Generators\Date as DateGenerator;
use PhpPact\Consumer\Matcher\Generators\DateTime as DateTimeGenerator;
use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Generators\Regex as RegexGenerator;
use PhpPact\Consumer\Matcher\Generators\Time as TimeGenerator;
use PhpPact\Consumer\Matcher\Generators\Uuid;
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
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\GeneratorAwareInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Matcher implementation. Builds the Pact FFI specification json for interaction publishing.
 * @see https://docs.pact.io/implementation_guides/rust/pact_ffi/integrationjson
 */
class Matcher
{
    public const EMAIL_FORMAT                        = "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$";
    public const ISO8601_DATE_FORMAT                 = '^([\\+-]?\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)$';
    public const ISO8601_DATETIME_FORMAT             = '^\\d{4}-[01]\\d-[0-3]\\dT[0-2]\\d:[0-5]\\d:[0-5]\\d([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?$';
    public const ISO8601_DATETIME_WITH_MILLIS_FORMAT = '^\\d{4}-[01]\\d-[0-3]\\dT[0-2]\\d:[0-5]\\d:[0-5]\\d\\.\\d{3}([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?$';
    public const ISO8601_TIME_FORMAT                 = '^(T\\d\\d:\\d\\d(:\\d\\d)?(\\.\\d+)?([+-][0-2]\\d(?:|:?[0-5]\\d)|Z)?)$';
    public const RFC3339_TIMESTAMP_FORMAT            = '^(Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s\\d{2}\\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\\s\\d{4}\\s\\d{2}:\\d{2}:\\d{2}\\s(\\+|-)\\d{4}$';
    public const UUID_V4_FORMAT                      = '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$';
    public const IPV4_FORMAT                         = '^(\\d{1,3}\\.)+\\d{1,3}$';
    public const IPV6_FORMAT                         = '^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$';
    public const HEX_FORMAT                          = '^[0-9a-fA-F]+$';

    public function __construct(private bool $plugin = false)
    {
    }

    /**
     * Alias for the `like()` function.
     */
    public function somethingLike(mixed $value): Type
    {
        return $this->like($value);
    }

    /**
     * This executes a type based match against the values, that is, they are equal if they are the same type.
     */
    public function like(mixed $value): Type
    {
        $matcher = new Type($value);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Array where each element must match the given value
     */
    public function eachLike(mixed $value): MinType
    {
        return $this->atLeastLike($value, 1);
    }

    /**
     * An array that has to have at least one element and each element must match the given value
     */
    public function atLeastOneLike(mixed $value): MinType
    {
        return $this->atLeastLike($value, 1);
    }

    /**
     * An array that has to have at least the required number of elements and each element must match the given value
     */
    public function atLeastLike(mixed $value, int $min): MinType
    {
        $matcher = new MinType($value, $min);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * An array that has to have at most the required number of elements and each element must match the given value
     */
    public function atMostLike(mixed $value, int $max): MaxType
    {
        $matcher = new MaxType($value, $max);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * An array whose size is constrained to the minimum and maximum number of elements and each element must match the given value
     */
    public function constrainedArrayLike(mixed $value, int $min, int $max): MinMaxType
    {
        $matcher = new MinMaxType($value, $min, $max);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Validate that values will match a regex pattern.
     *
     * @param string|string[]|null $values
     *
     * @throws MatcherException
     */
    public function term(string|array|null $values, string $pattern): Regex
    {
        $matcher = new Regex($pattern, $values ?? '');

        return $matcher
            ->withGenerator(null === $values ? new RegexGenerator($pattern) : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Alias for the term matcher.
     *
     * @param string|string[]|null $values
     *
     * @throws MatcherException
     */
    public function regex(string|array|null $values, string $pattern): Regex
    {
        return $this->term($values, $pattern);
    }

    /**
     * ISO8601 date format wrapper for the term matcher.
     *
     * @param string $value valid ISO8601 date, example: 2010-01-01
     *
     * @throws MatcherException
     */
    public function dateISO8601(string $value = '2013-02-01'): Regex
    {
        return $this->term($value, self::ISO8601_DATE_FORMAT);
    }

    /**
     * ISO8601 Time Matcher, matches a pattern of the format "'T'HH:mm:ss".
     *
     * @param string $value
     *
     * @throws MatcherException
     */
    public function timeISO8601(string $value = 'T22:44:30.652Z'): Regex
    {
        return $this->term($value, self::ISO8601_TIME_FORMAT);
    }

    /**
     * ISO8601 DateTime matcher.
     *
     * @param string $value
     *
     * @throws MatcherException
     */
    public function dateTimeISO8601(string $value = '2015-08-06T16:53:10+01:00'): Regex
    {
        return $this->term($value, self::ISO8601_DATETIME_FORMAT);
    }

    /**
     * ISO8601 DateTime matcher with required millisecond precision.
     *
     * @param string $value
     *
     * @throws MatcherException
     */
    public function dateTimeWithMillisISO8601(string $value = '2015-08-06T16:53:10.123+01:00'): Regex
    {
        return $this->term($value, self::ISO8601_DATETIME_WITH_MILLIS_FORMAT);
    }

    /**
     * RFC3339 Timestamp matcher, a subset of ISO8609.
     *
     * @param string $value
     *
     * @throws MatcherException
     */
    public function timestampRFC3339(string $value = 'Mon, 31 Oct 2016 15:21:41 -0400'): Regex
    {
        return $this->term($value, self::RFC3339_TIMESTAMP_FORMAT);
    }

    public function boolean(?bool $value = null): Type
    {
        return $this->like($value ?? true)
            ->withGenerator(is_null($value) ? new RandomBoolean() : null);
    }

    public function integer(?int $value = null): Type
    {
        return $this->like($value ?? 13)
            ->withGenerator(is_null($value) ? new RandomInt() : null);
    }

    public function decimal(?float $value = null): Type
    {
        return $this->like($value ?? 13.01)
            ->withGenerator(null === $value ? new RandomDecimal() : null);
    }

    public function booleanV3(?bool $value = null): Boolean
    {
        $matcher = new Boolean($value ?? false);

        return $matcher
            ->withGenerator(null === $value ? new RandomBoolean() : null)
            ->withFormatter($this->createFormatter());
    }

    public function integerV3(?int $value = null): Integer
    {
        $matcher = new Integer($value ?? 13);

        return $matcher
            ->withGenerator(null === $value ? new RandomInt() : null)
            ->withFormatter($this->createFormatter());
    }

    public function decimalV3(?float $value = null): Decimal
    {
        $matcher = new Decimal($value ?? 13.01);

        return $matcher
            ->withGenerator(null === $value ? new RandomDecimal() : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * @throws MatcherException
     */
    public function hexadecimal(?string $value = null): Regex
    {
        $matcher = new Regex(self::HEX_FORMAT, $value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new RandomHexadecimal() : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * @throws MatcherException
     */
    public function uuid(?string $value = null): Regex
    {
        $matcher = new Regex(self::UUID_V4_FORMAT, $value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new Uuid() : null)
            ->withFormatter($this->createFormatter());
    }

    public function ipv4Address(?string $ip = '127.0.0.13'): Regex
    {
        return $this->term($ip, self::IPV4_FORMAT);
    }

    public function ipv6Address(?string $ip = '::ffff:192.0.2.128'): Regex
    {
        return $this->term($ip, self::IPV6_FORMAT);
    }

    public function email(?string $email = 'hello@pact.io'): Regex
    {
        return $this->term($email, self::EMAIL_FORMAT);
    }

    /**
     * Value that must be null. This will only match the JSON Null value. For other content types, it will
     * match if the attribute is missing.
     */
    public function nullValue(): NullValue
    {
        $matcher = new NullValue();

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Matches the string representation of a value against the date format.
     *
     * NOTE: Java's datetime format is used, not PHP's datetime format
     * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
     * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public function date(string $format = 'yyyy-MM-dd', ?string $value = null): Date
    {
        $matcher = new Date($format, $value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new DateGenerator($format) : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Matches the string representation of a value against the time format.
     *
     * NOTE: Java's datetime format is used, not PHP's datetime format
     * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
     * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public function time(string $format = 'HH:mm:ss', ?string $value = null): Time
    {
        $matcher = new Time($format, $value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new TimeGenerator() : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Matches the string representation of a value against the datetime format.
     *
     * NOTE: Java's datetime format is used, not PHP's datetime format
     * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
     * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public function datetime(string $format = "yyyy-MM-dd'T'HH:mm:ss", ?string $value = null): DateTime
    {
        $matcher = new DateTime($format, $value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new DateTimeGenerator() : null)
            ->withFormatter($this->createFormatter());
    }

    public function string(?string $value = null): StringValue
    {
        $matcher = new StringValue($value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new RandomString() : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Generates a value that is looked up from the provider state context using the given expression
     */
    public function fromProviderState(MatcherInterface&GeneratorAwareInterface $matcher, string $expression): MatcherInterface&GeneratorAwareInterface
    {
        return $matcher->withGenerator(new ProviderState($expression));
    }

    /**
     * Value that must be equal to the example. This is mainly used to reset the matching rules which cascade.
     */
    public function equal(mixed $value): Equality
    {
        $matcher = new Equality($value);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Value that must include the example value as a substring.
     */
    public function includes(string $value): Includes
    {
        $matcher = new Includes($value);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Value must be a number
     *
     * @param int|float|null $value Example value. If omitted a random integer value will be generated.
     */
    public function number(int|float|null $value = null): Number
    {
        $matcher = new Number($value ?? 13);

        return $matcher
            ->withGenerator(null === $value ? new RandomInt() : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Matches the items in an array against a number of variants. Matching is successful if each variant
     * occurs once in the array. Variants may be objects containing matching rules.
     *
     * @param array<mixed> $variants
     */
    public function arrayContaining(array $variants): ArrayContains
    {
        $matcher = new ArrayContains($variants);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Value must be present and not empty (not null or the empty string or empty array or empty object)
     */
    public function notEmpty(mixed $value): NotEmpty
    {
        $matcher = new NotEmpty($value);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Value must be valid based on the semver specification
     */
    public function semver(?string $value = null): Semver
    {
        $matcher = new Semver($value ?? '');

        return $matcher
            ->withGenerator(null === $value ? new RegexGenerator('\d+\.\d+\.\d+') : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Matches the response status code.
     */
    public function statusCode(string|HttpStatus $status, ?int $value = null): StatusCode
    {
        if (is_string($status)) {
            try {
                $status = HttpStatus::from($status);
            } catch (\Throwable $th) {
                $all = implode(', ', array_map(
                    fn (HttpStatus $status) => $status->value,
                    HttpStatus::cases()
                ));
                throw new InvalidHttpStatusException(sprintf("Status '%s' is not supported. Supported status are: %s", $status, $all));
            }
        }

        if (null === $value) {
            $value = 0;
            $range = $status->range();

            $generator = new RandomInt($range->min, $range->max);
        } else {
            $generator = null;
        }

        $matcher = new StatusCode($status, $value);

        return $matcher
            ->withGenerator($generator)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Match the values in a map, ignoring the keys
     *
     * @deprecated use eachKey or eachValue
     *
     * @param array<mixed> $values
     */
    public function values(array $values): Values
    {
        $matcher = new Values($values);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Match binary data by its content type (magic file check)
     */
    public function contentType(string $contentType): ContentType
    {
        $matcher = new ContentType($contentType);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Allows defining matching rules to apply to the keys in a map
     *
     * @param array<string, mixed>|object $values
     * @param MatcherInterface[]   $rules
     */
    public function eachKey(array|object $values, array $rules): EachKey
    {
        $matcher = new EachKey($values, $rules);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * Allows defining matching rules to apply to the values in a collection. For maps, delgates to the Values matcher.
     *
     * @param array<string, mixed>|object $values
     * @param MatcherInterface[]   $rules
     */
    public function eachValue(array|object $values, array $rules): EachValue
    {
        $matcher = new EachValue($values, $rules);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * @throws MatcherException
     */
    public function url(string $url, string $regex, bool $useMockServerBasePath = true): Regex
    {
        $matcher = new Regex($regex, $useMockServerBasePath ? '' : $url);

        return $matcher
            ->withGenerator($useMockServerBasePath ? new MockServerURL($regex, $url) : null)
            ->withFormatter($this->createFormatter());
    }

    /**
     * Generates a value that is looked up from the provider state context using the given expression
     */
    public function matchingField(string $fieldName): MatchingField
    {
        $matcher = new MatchingField($fieldName);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * @param array<mixed>|object $value
     * @param MatcherInterface[] $matchers
     */
    public function matchAll(object|array $value, array $matchers): MatchAll
    {
        $matcher = new MatchAll($value, $matchers);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * An array that has to have at least the required number of elements
     */
    public function atLeast(int $min): Min
    {
        $matcher = new Min($min);

        return $matcher->withFormatter($this->createFormatter());
    }

    /**
     * An array that has to have at most the required number of elements
     */
    public function atMost(int $max): Max
    {
        $matcher = new Max($max);

        return $matcher->withFormatter($this->createFormatter());
    }

    private function createFormatter(): FormatterInterface
    {
        return $this->plugin ? new ExpressionFormatter() : new JsonFormatter();
    }
}
