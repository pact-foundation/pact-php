<?php

namespace PhpPact\Consumer\Matcher;

use PhpPact\Consumer\Matcher\Exception\MatcherException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
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
        return new Type($value);
    }

    /**
     * Expect an array of similar data as the value passed in.
     */
    public function eachLike(mixed $value): MinType
    {
        return $this->atLeastLike($value, 1);
    }

    /**
     * @param mixed $value example of what the expected data would be
     * @param int   $min   minimum number of objects to verify against
     */
    public function atLeastLike(mixed $value, int $min): MinType
    {
        return new MinType(array_fill(0, $min, $value), $min);
    }

    public function atMostLike(mixed $value, int $max): MaxType
    {
        return new MaxType([$value], $max);
    }

    /**
     * @param mixed    $value example of what the expected data would be
     * @param int      $min   minimum number of objects to verify against
     * @param int      $max   maximum number of objects to verify against
     * @param int|null $count number of examples to generate, defaults to one
     *
     * @throws MatcherException
     */
    public function constrainedArrayLike(mixed $value, int $min, int $max, ?int $count = null): MinMaxType
    {
        $elements = $count ?? $min;
        if ($count !== null) {
            if ($count < $min) {
                throw new MatcherException(
                    "constrainedArrayLike has a minimum of {$min} but {$count} elements where requested." .
                    ' Make sure the count is greater than or equal to the min.'
                );
            } elseif ($count > $max) {
                throw new MatcherException(
                    "constrainedArrayLike has a maximum of {$max} but {$count} elements where requested." .
                    ' Make sure the count is less than or equal to the max.'
                );
            }
        }

        return new MinMaxType(array_fill(0, $elements, $value), $min, $max);
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
        return new Regex($pattern, $values);
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

    public function boolean(): Type
    {
        return $this->like(true);
    }

    public function integer(int $int = 13): Type
    {
        return $this->like($int);
    }

    public function decimal(float $float = 13.01): Type
    {
        return $this->like($float);
    }

    public function booleanV3(?bool $value = null): Boolean
    {
        return new Boolean($value);
    }

    public function integerV3(?int $value = null): Integer
    {
        return new Integer($value);
    }

    public function decimalV3(?float $value = null): Decimal
    {
        return new Decimal($value);
    }

    /**
     * @throws MatcherException
     */
    public function hexadecimal(?string $value = null): Regex
    {
        $matcher = new Regex(self::HEX_FORMAT, $value);

        if (null === $value) {
            $matcher->setGenerator(new RandomHexadecimal());
        }

        return $matcher;
    }

    /**
     * @throws MatcherException
     */
    public function uuid(?string $value = null): Regex
    {
        $matcher = new Regex(self::UUID_V4_FORMAT, $value);

        if (null === $value) {
            $matcher->setGenerator(new Uuid());
        }

        return $matcher;
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
        return new NullValue();
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
        return new Date($format, $value);
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
        return new Time($format, $value);
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
        return new DateTime($format, $value);
    }

    public function string(?string $value = null): StringValue
    {
        return new StringValue($value);
    }

    /**
     * Generates a value that is looked up from the provider state context using the given expression
     *
     * @throws MatcherNotSupportedException
     */
    public function fromProviderState(MatcherInterface $matcher, string $expression): MatcherInterface
    {
        if (!$matcher instanceof GeneratorAwareInterface) {
            throw new MatcherNotSupportedException(sprintf("Matcher '%s' must be generator aware", $matcher->getType()));
        }

        $matcher->setGenerator(new ProviderState($expression));

        return $matcher;
    }

    /**
     * Value that must be equal to the example. This is mainly used to reset the matching rules which cascade.
     */
    public function equal(mixed $value): Equality
    {
        return new Equality($value);
    }

    /**
     * Value that must include the example value as a substring.
     */
    public function includes(string $value): Includes
    {
        return new Includes($value);
    }

    /**
     * Value must be a number
     *
     * @param int|float|null $value Example value. If omitted a random integer value will be generated.
     */
    public function number(int|float|null $value = null): Number
    {
        return new Number($value);
    }

    /**
     * Matches the items in an array against a number of variants. Matching is successful if each variant
     * occurs once in the array. Variants may be objects containing matching rules.
     *
     * @param array<mixed> $variants
     */
    public function arrayContaining(array $variants): ArrayContains
    {
        return new ArrayContains($variants);
    }

    /**
     * Value must be present and not empty (not null or the empty string or empty array or empty object)
     */
    public function notEmpty(mixed $value): NotEmpty
    {
        return new NotEmpty($value);
    }

    /**
     * Value must be valid based on the semver specification
     */
    public function semver(string $value): Semver
    {
        return new Semver($value);
    }

    /**
     * Matches the response status code.
     */
    public function statusCode(string $status, ?int $value = null): StatusCode
    {
        return new StatusCode($status, $value);
    }

    /**
     * Match the values in a map, ignoring the keys
     *
     * @param array<mixed> $values
     */
    public function values(array $values): Values
    {
        return new Values($values);
    }

    /**
     * Match binary data by its content type (magic file check)
     */
    public function contentType(string $contentType): ContentType
    {
        return new ContentType($contentType);
    }

    /**
     * Allows defining matching rules to apply to the keys in a map
     *
     * @param array<string, mixed> $values
     * @param array<mixed>         $rules
     */
    public function eachKey(array $values, array $rules): EachKey
    {
        return new EachKey($values, $rules);
    }

    /**
     * Allows defining matching rules to apply to the values in a collection. For maps, delgates to the Values matcher.
     *
     * @param array<string, mixed> $values
     * @param array<mixed>         $rules
     */
    public function eachValue(array $values, array $rules): EachValue
    {
        return new EachValue($values, $rules);
    }
}
