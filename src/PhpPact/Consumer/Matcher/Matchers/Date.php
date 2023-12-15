<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Generators\Date as DateGenerator;

/**
 * Matches the string representation of a value against the date format.
 *
 * NOTE: Java's datetime format is used, not PHP's datetime format
 * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
 * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
 */
class Date extends AbstractDateTime
{
    public function __construct(string $format = 'yyyy-MM-dd', ?string $value = null)
    {
        if ($value === null) {
            $this->setGenerator(new DateGenerator($format));
        }
        parent::__construct($format, $value);
    }

    public function getType(): string
    {
        return 'date';
    }
}
