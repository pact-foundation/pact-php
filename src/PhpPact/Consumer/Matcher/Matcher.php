<?php

namespace PhpPact\Consumer\Matcher;

use Exception;

use function preg_last_error;
use function preg_match;

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
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function somethingLike(mixed $value): array
    {
        return $this->like($value);
    }

    /**
     * @param mixed $value example of what the expected data would be
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function like(mixed $value): array
    {
        return [
            'value'   => $value,
            'pact:matcher:type' => 'type',
        ];
    }

    /**
     * Expect an array of similar data as the value passed in.
     *
     * @return array<string, mixed>
     */
    public function eachLike(mixed $value): array
    {
        return $this->atLeastLike($value, 1);
    }

    /**
     * @param mixed $value example of what the expected data would be
     * @param int   $min   minimum number of objects to verify against
     *
     * @return array<string, mixed>
     */
    public function atLeastLike(mixed $value, int $min): array
    {
        return [
            'value' => array_fill(0, $min, $value),
            'pact:matcher:type' => 'type',
            'min' => $min,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function atMostLike(mixed $value, int $max): array
    {
        return [
            'value' => [$value],
            'pact:matcher:type' => 'type',
            'max' => $max,
        ];
    }

    /**
     * @param mixed    $value example of what the expected data would be
     * @param int      $min   minimum number of objects to verify against
     * @param int      $max   maximum number of objects to verify against
     * @param int|null $count number of examples to generate, defaults to one
     *
     * @return array<string, mixed>
     */
    public function constrainedArrayLike(mixed $value, int $min, int $max, ?int $count = null): array
    {
        $elements = $count ?? $min;
        if ($count !== null) {
            if ($count < $min) {
                throw new Exception(
                    "constrainedArrayLike has a minimum of {$min} but {$count} elements where requested." .
                    ' Make sure the count is greater than or equal to the min.'
                );
            } elseif ($count > $max) {
                throw new Exception(
                    "constrainedArrayLike has a maximum of {$max} but {$count} elements where requested." .
                    ' Make sure the count is less than or equal to the max.'
                );
            }
        }

        return [
            'min' => $min,
            'max' => $max,
            'pact:matcher:type' => 'type',
            'value' => array_fill(0, $elements, $value),
        ];
    }

    /**
     * Validate that a value will match a regex pattern.
     *
     * @param string|null $value   example of what the expected data would be
     * @param string $pattern valid Ruby regex pattern
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function term(?string $value, string $pattern): array
    {
        if (null === $value) {
            return [
                'regex'               => $pattern,
                'pact:matcher:type'   => 'regex',
                'pact:generator:type' => 'Regex',
            ];
        }

        $result = preg_match("/$pattern/", $value);

        if ($result === false || $result === 0) {
            $errorCode = preg_last_error();

            throw new Exception("The pattern {$pattern} is not valid for value {$value}. Failed with error code {$errorCode}.");
        }

        return [
            'value'             => $value,
            'regex'             => $pattern,
            'pact:matcher:type' => 'regex',
        ];
    }

    /**
     * Alias for the term matcher.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function regex(?string $value, string $pattern): array
    {
        return $this->term($value, $pattern);
    }

    /**
     * ISO8601 date format wrapper for the term matcher.
     *
     * @param string $value valid ISO8601 date, example: 2010-01-01
     *
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function dateISO8601(string $value = '2013-02-01'): array
    {
        return $this->term($value, self::ISO8601_DATE_FORMAT);
    }

    /**
     * ISO8601 Time Matcher, matches a pattern of the format "'T'HH:mm:ss".
     *
     * @param string $value
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function timeISO8601(string $value = 'T22:44:30.652Z'): array
    {
        return $this->term($value, self::ISO8601_TIME_FORMAT);
    }

    /**
     * ISO8601 DateTime matcher.
     *
     * @param string $value
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function dateTimeISO8601(string $value = '2015-08-06T16:53:10+01:00'): array
    {
        return $this->term($value, self::ISO8601_DATETIME_FORMAT);
    }

    /**
     * ISO8601 DateTime matcher with required millisecond precision.
     *
     * @param string $value
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function dateTimeWithMillisISO8601(string $value = '2015-08-06T16:53:10.123+01:00'): array
    {
        return $this->term($value, self::ISO8601_DATETIME_WITH_MILLIS_FORMAT);
    }

    /**
     * RFC3339 Timestamp matcher, a subset of ISO8609.
     *
     * @param string $value
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function timestampRFC3339(string $value = 'Mon, 31 Oct 2016 15:21:41 -0400'): array
    {
        return $this->term($value, self::RFC3339_TIMESTAMP_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function boolean(): array
    {
        return $this->like(true);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function integer(int $int = 13): array
    {
        return $this->like($int);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function decimal(float $float = 13.01): array
    {
        return $this->like($float);
    }

    /**
     * @return array<string, mixed>
     */
    public function booleanV3(?bool $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'RandomBoolean',
                'pact:matcher:type'   => 'boolean',
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'boolean',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function integerV3(?int $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'RandomInt',
                'pact:matcher:type'   => 'integer',
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function decimalV3(?float $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'RandomDecimal',
                'pact:matcher:type'   => 'decimal',
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'decimal',
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function hexadecimal(?string $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'RandomHexadecimal',
            ] + $this->term(null, self::HEX_FORMAT);
        }

        return $this->term($value, self::HEX_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function uuid(?string $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'Uuid',
            ] + $this->term(null, self::UUID_V4_FORMAT);
        }

        return $this->term($value, self::UUID_V4_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function ipv4Address(string $ip = '127.0.0.13'): array
    {
        return $this->term($ip, self::IPV4_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function ipv6Address(string $ip = '::ffff:192.0.2.128'): array
    {
        return $this->term($ip, self::IPV6_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function email(string $email = 'hello@pact.io'): array
    {
        return $this->term($email, self::EMAIL_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function ipv4AddressV3(?string $ip = null): array
    {
        if (null === $ip) {
            return $this->term(null, self::IPV4_FORMAT);
        }

        return $this->ipv4Address($ip);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function ipv6AddressV3(?string $ip = null): array
    {
        if (null === $ip) {
            return $this->term(null, self::IPV6_FORMAT);
        }

        return $this->ipv6Address($ip);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function emailV3(?string $email = null): array
    {
        if (null === $email) {
            return $this->term(null, self::EMAIL_FORMAT);
        }

        return $this->email($email);
    }

    /**
     * Value that must be null. This will only match the JSON Null value. For other content types, it will
     * match if the attribute is missing.
     *
     * @return array<string, string>
     */
    public function nullValue(): array
    {
        return [
            'pact:matcher:type' => 'null',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function date(string $format = 'yyyy-MM-dd', ?string $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'Date',
                'pact:matcher:type'   => 'date',
                'format'              => $format,
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'date',
            'format'            => $format,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function time(string $format = 'HH:mm::ss', ?string $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'Time',
                'pact:matcher:type'   => 'time',
                'format'              => $format,
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'time',
            'format'            => $format,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function datetime(string $format = "YYYY-mm-DD'T'HH:mm:ss", ?string $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'DateTime',
                'pact:matcher:type'   => 'datetime',
                'format'              => $format,
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'datetime',
            'format'            => $format,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function string(?string $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'RandomString',
            ] + $this->like('some string'); // No matcher for string?
        }

        return $this->like($value); // No matcher for string?
    }

    /**
     * @param array<string, mixed> $macher
     *
     * @return array<string, mixed>
     */
    public function fromProviderState(array $macher, string $expression): array
    {
        return $macher + [
            'pact:generator:type' => 'ProviderState',
            'expression'          => $expression,
        ];
    }

    /**
     * Value that must be equal to the example. This is mainly used to reset the matching rules which cascade.
     *
     * @return array<string, mixed>
     */
    public function equal(mixed $value): array
    {
        return [
            'pact:matcher:type' => 'equality',
            'value'             => $value,
        ];
    }

    /**
     * Value that must include the example value as a substring.
     *
     * @return array<string, mixed>
     */
    public function includes(string $value): array
    {
        return [
            'pact:matcher:type' => 'include',
            'value'             => $value,
        ];
    }

    /**
     * Value must be a number
     *
     * @param int|float|null $value Example value. If omitted a random integer value will be generated.
     *
     * @return array<string, mixed>
     */
    public function number(int|float|null $value = null): array
    {
        if (null === $value) {
            return [
                'pact:generator:type' => 'RandomInt',
                'pact:matcher:type'   => 'number',
            ];
        }

        return [
            'value'             => $value,
            'pact:matcher:type' => 'number',
        ];
    }

    /**
     * Matches the items in an array against a number of variants. Matching is successful if each variant
     * occurs once in the array. Variants may be objects containing matching rules.
     *
     * @param array<mixed> $variants
     *
     * @return array<string, mixed>
     */
    public function arrayContaining(array $variants): array
    {
        return [
            'pact:matcher:type' => 'arrayContains',
            'variants'          => $variants,
        ];
    }

    /**
     * Value must be present and not empty (not null or the empty string or empty array or empty object)
     *
     * @return array<string, mixed>
     */
    public function notEmpty(mixed $value): array
    {
        return [
            'value'             => $value,
            'pact:matcher:type' => 'notEmpty',
        ];
    }

    /**
     * Value must be valid based on the semver specification
     *
     * @return array<string, mixed>
     */
    public function semver(string $value): array
    {
        return [
            'value'             => $value,
            'pact:matcher:type' => 'semver',
        ];
    }

    /**
     * Matches the response status code.
     *
     * @return array<string, mixed>
     */
    public function statusCode(string $status): array
    {
        if (!in_array($status, HttpStatus::all())) {
            throw new Exception(sprintf("Status '%s' is not supported. Supported status are: %s", $status, implode(', ', HttpStatus::all())));
        }

        return [
            'status'             => $status,
            'pact:matcher:type' => 'statusCode',
        ];
    }

    /**
     * Match the values in a map, ignoring the keys
     *
     * @param array<mixed> $values
     *
     * @return array<string, mixed>
     */
    public function values(array $values): array
    {
        return [
            'value'             => array_values($values),
            'pact:matcher:type' => 'values',
        ];
    }

    /**
     * Match binary data by its content type (magic file check)
     *
     * @return array<string, mixed>
     */
    public function contentType(string $contentType): array
    {
        return [
            'value'             => $contentType,
            'pact:matcher:type' => 'contentType',
        ];
    }
}
