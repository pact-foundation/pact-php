<?php

namespace PhpPact\Consumer\Matcher;

/**
 * Matcher implementation. Builds the Ruby Mock Server specification json for interaction publishing.
 * Class Matcher
 */
class Matcher
{
    const ISO8601_DATE_FORMAT = '^([\\+-]?\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)$';

    /**
     * @param mixed $value example of what the expected data would be
     *
     * @throws \Exception
     *
     * @return array
     */
    public function like($value): array
    {
        if ($value === null) {
            throw new \Exception('Value must not be null.');
        }

        return [
            'contents'   => $value,
            'json_class' => 'Pact::SomethingLike'
        ];
    }

    /**
     * Expect an array of similar data as the value passed in.
     *
     * @param mixed $value example of what the expected data would be
     * @param int   $min   minimum number of objects to verify against
     *
     * @return array
     */
    public function eachLike($value, int $min = null): array
    {
        $result = [
            'contents'   => $value,
            'json_class' => 'Pact::ArrayLike'
        ];

        if ($min !== null) {
            $result['min'] = $min;
        }

        return $result;
    }

    /**
     * Validate that a value will match a regex pattern.
     *
     * @param mixed  $value   example of what the expected data would be
     * @param string $pattern valid Ruby regex pattern
     *
     * @throws \Exception
     *
     * @return array
     */
    public function term($value, string $pattern): array
    {
        $result = \preg_match("/$pattern/", $value);

        if ($result === false || $result === 0) {
            $errorCode = \preg_last_error();

            throw new \Exception("The pattern {$pattern} is not valid for value {$value}. Failed with error code {$errorCode}.");
        }

        return [
            'data' => [
                'generate' => $value,
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => $pattern
                ]
            ],
            'json_class' => 'Pact::Term'
        ];
    }

    /**
     * Alias for the term matcher.
     *
     * @param mixed  $value   example value
     * @param string $pattern valid Ruby regex pattern
     *
     * @return array
     */
    public function regex($value, string $pattern)
    {
        return $this->term($value, $pattern);
    }

    /**
     * ISO8601 date format wrapper for the term matcher.
     *
     * @param string $value valid ISO8601 date, example: 2010-01-01
     *
     * @return array
     */
    public function dateISO8601(string $value): array
    {
        return $this->term($value, self::ISO8601_DATE_FORMAT);
    }
}
