<?php

namespace PhpPact\Consumer\Matcher;

use Exception;

use function preg_last_error;
use function preg_match;

/**
 * Matcher implementation. Builds the Ruby Mock Server specification json for interaction publishing.
 */
class Matcher
{
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
        if ($value === null) {
            throw new \Exception('Value must not be null.');
        }

        return [
            'contents'   => $value,
            'json_class' => 'Pact::SomethingLike',
        ];
    }

    /**
     * Expect an array of similar data as the value passed in.
     *
     * @param mixed $value example of what the expected data would be
     * @param int   $min   minimum number of objects to verify against
     *
     * @return array<string, mixed>
     */
    public function eachLike(mixed $value, int $min = 1): array
    {
        $result = [
            'contents'   => $value,
            'json_class' => 'Pact::ArrayLike',
        ];

        $result['min'] = $min;

        return $result;
    }

    /**
     * Validate that a value will match a regex pattern.
     *
     * @param mixed  $value   example of what the expected data would be
     * @param string $pattern valid Ruby regex pattern
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function term(mixed $value, string $pattern): array
    {
        $result = preg_match("/$pattern/", $value);

        if ($result === false || $result === 0) {
            $errorCode = preg_last_error();

            throw new Exception("The pattern {$pattern} is not valid for value {$value}. Failed with error code {$errorCode}.");
        }

        return [
            'data' => [
                'generate' => $value,
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => $pattern,
                ],
            ],
            'json_class' => 'Pact::Term',
        ];
    }

    /**
     * Alias for the term matcher.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function regex(mixed $value, string $pattern): array
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
     *
     * @throws Exception
     */
    public function hexadecimal(string $hex = '3F'): array
    {
        return $this->term($hex, self::HEX_FORMAT);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function uuid(string $uuid = 'ce118b6e-d8e1-11e7-9296-cec278b6b50a'): array
    {
        return $this->term($uuid, self::UUID_V4_FORMAT);
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
}
