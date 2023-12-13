<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a time value for the provided format.
 * If no format is provided, ISO time format is used.
 * If an expression is given, it will be evaluated to generate the time, otherwise 'now' will be used
 *
 * Example format: HH:mm:ss
 * Example expression: +1 hour
 *
 * NOTE: Java's datetime format is used, not PHP's datetime format
 * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
 * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
 */
class Time extends AbstractDateTime
{
    protected function getType(): string
    {
        return 'Time';
    }
}
