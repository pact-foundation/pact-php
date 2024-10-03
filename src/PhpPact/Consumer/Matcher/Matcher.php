<?php

namespace PhpPact\Consumer\Matcher;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PhpPact\Consumer\Matcher\Exception\MatcherException;
use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
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
    public function somethingLike(mixed $value): MatcherInterface
    {
        return $this->like($value);
    }

    /**
     * This executes a type based match against the values, that is, they are equal if they are the same type.
     */
    public function like(mixed $value): MatcherInterface
    {
        return $this->withFormatter(new Type($value));
    }

    /**
     * Array where each element must match the given value
     */
    public function eachLike(mixed $value): MatcherInterface
    {
        return $this->atLeastLike($value, 1);
    }

    /**
     * An array that has to have at least one element and each element must match the given value
     */
    public function atLeastOneLike(mixed $value): MatcherInterface
    {
        return $this->atLeastLike($value, 1);
    }

    /**
     * An array that has to have at least the required number of elements and each element must match the given value
     */
    public function atLeastLike(mixed $value, int $min): MatcherInterface
    {
        return $this->withFormatter(new MinType($value, $min));
    }

    /**
     * An array that has to have at most the required number of elements and each element must match the given value
     */
    public function atMostLike(mixed $value, int $max): MatcherInterface
    {
        return $this->withFormatter(new MaxType($value, $max));
    }

    /**
     * An array whose size is constrained to the minimum and maximum number of elements and each element must match the given value
     */
    public function constrainedArrayLike(mixed $value, int $min, int $max): MatcherInterface
    {
        return $this->withFormatter(new MinMaxType($value, $min, $max));
    }

    /**
     * Validate that values will match a regex pattern.
     *
     * @param string|string[]|null $values
     *
     * @throws MatcherException
     */
    public function term(string|array|null $values, string $pattern): MatcherInterface
    {
        return $this->withFormatter(new Regex($pattern, $values));
    }

    /**
     * Alias for the term matcher.
     *
     * @param string|string[]|null $values
     *
     * @throws MatcherException
     */
    public function regex(string|array|null $values, string $pattern): MatcherInterface
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
    public function dateISO8601(string $value = '2013-02-01'): MatcherInterface
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
    public function timeISO8601(string $value = 'T22:44:30.652Z'): MatcherInterface
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
    public function dateTimeISO8601(string $value = '2015-08-06T16:53:10+01:00'): MatcherInterface
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
    public function dateTimeWithMillisISO8601(string $value = '2015-08-06T16:53:10.123+01:00'): MatcherInterface
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
    public function timestampRFC3339(string $value = 'Mon, 31 Oct 2016 15:21:41 -0400'): MatcherInterface
    {
        return $this->term($value, self::RFC3339_TIMESTAMP_FORMAT);
    }

    public function boolean(): MatcherInterface
    {
        return $this->like(true);
    }

    public function integer(int $int = 13): MatcherInterface
    {
        return $this->like($int);
    }

    public function decimal(float $float = 13.01): MatcherInterface
    {
        return $this->like($float);
    }

    public function booleanV3(?bool $value = null): MatcherInterface
    {
        return $this->withFormatter(new Boolean($value));
    }

    public function integerV3(?int $value = null): MatcherInterface
    {
        return $this->withFormatter(new Integer($value));
    }

    public function decimalV3(?float $value = null): MatcherInterface
    {
        return $this->withFormatter(new Decimal($value));
    }

    /**
     * @throws MatcherException
     */
    public function hexadecimal(?string $value = null): MatcherInterface
    {
        $matcher = new Regex(self::HEX_FORMAT, $value);

        if (null === $value) {
            $matcher->setGenerator(new RandomHexadecimal());
        }

        return $this->withFormatter($matcher);
    }

    /**
     * @throws MatcherException
     */
    public function uuid(?string $value = null): MatcherInterface
    {
        $matcher = new Regex(self::UUID_V4_FORMAT, $value);

        if (null === $value) {
            $matcher->setGenerator(new Uuid());
        }

        return $this->withFormatter($matcher);
    }

    public function ipv4Address(?string $ip = '127.0.0.13'): MatcherInterface
    {
        return $this->term($ip, self::IPV4_FORMAT);
    }

    public function ipv6Address(?string $ip = '::ffff:192.0.2.128'): MatcherInterface
    {
        return $this->term($ip, self::IPV6_FORMAT);
    }

    public function email(?string $email = 'hello@pact.io'): MatcherInterface
    {
        return $this->term($email, self::EMAIL_FORMAT);
    }

    /**
     * Value that must be null. This will only match the JSON Null value. For other content types, it will
     * match if the attribute is missing.
     */
    public function nullValue(): MatcherInterface
    {
        return $this->withFormatter(new NullValue());
    }

    /**
     * Matches the string representation of a value against the date format.
     *
     * NOTE: Java's datetime format is used, not PHP's datetime format
     * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
     * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public function date(string $format = 'yyyy-MM-dd', ?string $value = null): MatcherInterface
    {
        return $this->withFormatter(new Date($format, $value));
    }

    /**
     * Matches the string representation of a value against the time format.
     *
     * NOTE: Java's datetime format is used, not PHP's datetime format
     * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
     * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public function time(string $format = 'HH:mm:ss', ?string $value = null): MatcherInterface
    {
        return $this->withFormatter(new Time($format, $value));
    }

    /**
     * Matches the string representation of a value against the datetime format.
     *
     * NOTE: Java's datetime format is used, not PHP's datetime format
     * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
     * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public function datetime(string $format = "yyyy-MM-dd'T'HH:mm:ss", ?string $value = null): MatcherInterface
    {
        return $this->withFormatter(new DateTime($format, $value));
    }

    public function string(?string $value = null): MatcherInterface
    {
        return $this->withFormatter(new StringValue($value));
    }

    /**
     * Generates a value that is looked up from the provider state context using the given expression
     */
    public function fromProviderState(MatcherInterface&GeneratorAwareInterface $matcher, string $expression): MatcherInterface
    {
        $matcher->setGenerator(new ProviderState($expression));

        return $matcher;
    }

    /**
     * Value that must be equal to the example. This is mainly used to reset the matching rules which cascade.
     */
    public function equal(mixed $value): MatcherInterface
    {
        return $this->withFormatter(new Equality($value));
    }

    /**
     * Value that must include the example value as a substring.
     */
    public function includes(string $value): MatcherInterface
    {
        return $this->withFormatter(new Includes($value));
    }

    /**
     * Value must be a number
     *
     * @param int|float|null $value Example value. If omitted a random integer value will be generated.
     */
    public function number(int|float|null $value = null): MatcherInterface
    {
        return $this->withFormatter(new Number($value));
    }

    /**
     * Matches the items in an array against a number of variants. Matching is successful if each variant
     * occurs once in the array. Variants may be objects containing matching rules.
     *
     * @param array<mixed> $variants
     */
    public function arrayContaining(array $variants): MatcherInterface
    {
        return $this->withFormatter(new ArrayContains($variants));
    }

    /**
     * Value must be present and not empty (not null or the empty string or empty array or empty object)
     */
    public function notEmpty(mixed $value): MatcherInterface
    {
        return $this->withFormatter(new NotEmpty($value));
    }

    /**
     * Value must be valid based on the semver specification
     */
    public function semver(string $value): MatcherInterface
    {
        return $this->withFormatter(new Semver($value));
    }

    /**
     * Matches the response status code.
     */
    public function statusCode(string|HttpStatus $status, ?int $value = null): MatcherInterface
    {
        return $this->withFormatter(new StatusCode($status, $value));
    }

    /**
     * Match the values in a map, ignoring the keys
     *
     * @deprecated use eachKey or eachValue
     *
     * @param array<mixed> $values
     */
    public function values(array $values): MatcherInterface
    {
        return $this->withFormatter(new Values($values));
    }

    /**
     * Match binary data by its content type (magic file check)
     */
    public function contentType(string $contentType): MatcherInterface
    {
        return $this->withFormatter(new ContentType($contentType));
    }

    /**
     * Allows defining matching rules to apply to the keys in a map
     *
     * @param array<string, mixed> $values
     * @param MatcherInterface[]   $rules
     */
    public function eachKey(array $values, array $rules): MatcherInterface
    {
        return $this->withFormatter(new EachKey($values, $rules));
    }

    /**
     * Allows defining matching rules to apply to the values in a collection. For maps, delgates to the Values matcher.
     *
     * @param array<string, mixed> $values
     * @param MatcherInterface[]   $rules
     */
    public function eachValue(array $values, array $rules): MatcherInterface
    {
        return $this->withFormatter(new EachValue($values, $rules));
    }

    /**
     * @throws MatcherException
     */
    public function url(string $url, string $regex, bool $useMockServerBasePath = true): MatcherInterface
    {
        $matcher = new Regex($regex, $useMockServerBasePath ? null : $url);

        if ($useMockServerBasePath) {
            $matcher->setGenerator(new MockServerURL($regex, $url));
        }

        return $this->withFormatter($matcher);
    }

    /**
     * Generates a value that is looked up from the provider state context using the given expression
     */
    public function matchingField(string $fieldName): MatcherInterface
    {
        return $this->withFormatter(new MatchingField($fieldName));
    }

    /**
     * @param array<mixed>|object $value
     * @param MatcherInterface[] $matchers
     */
    public function matchAll(object|array $value, array $matchers): MatcherInterface
    {
        return $this->withFormatter(new MatchAll($value, $matchers));
    }

    /**
     * An array that has to have at least the required number of elements
     */
    public function atLeast(int $min): MatcherInterface
    {
        return $this->withFormatter(new MinType(null, $min, false));
    }

    /**
     * An array that has to have at most the required number of elements
     */
    public function atMost(int $max): MatcherInterface
    {
        return $this->withFormatter(new MaxType(null, $max, false));
    }

    private function withFormatter(MatcherInterface $matcher): MatcherInterface
    {
        if ($this->plugin) {
            $formatter = $matcher->createExpressionFormatter();
        } else {
            $formatter = $matcher->createJsonFormatter();
        }
        $matcher->setFormatter($formatter);

        return $matcher;
    }
}
