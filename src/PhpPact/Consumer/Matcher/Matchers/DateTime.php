<?php

namespace PhpPact\Consumer\Matcher\Matchers;

/**
 * Matches the string representation of a value against the datetime format.
 *
 * NOTE: Java's datetime format is used, not PHP's datetime format
 * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
 * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
 */
class DateTime extends AbstractDateTime
{
    public function __construct(string $format = "yyyy-MM-dd'T'HH:mm:ss", string $value = '')
    {
        parent::__construct($format, $value);
    }

    public function getType(): string
    {
        return 'datetime';
    }
}
