<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Formatters\ValueRequiredFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PHPUnit\Framework\TestCase;

class ValueRequiredFormatterTest extends TestCase
{
    /**
     * @testWith [true,  "2001-01-02", {"pact:matcher:type": "date", "pact:generator:type": "RandomString", "format": "yyyy-MM-dd", "size": 10, "value": "2001-01-02"}]
     *           [false, "2002-02-03", {"pact:matcher:type": "date", "format": "yyyy-MM-dd", "value": "2002-02-03"}]
     *           [true,  null,         {"pact:matcher:type": "date", "pact:generator:type": "RandomString", "format": "yyyy-MM-dd", "size": 10, "value": null}]
     *           [false, null,         {"pact:matcher:type": "date", "format": "yyyy-MM-dd", "value": null}]
     */
    public function testFormat(bool $hasGenerator, ?string $value, array $result): void
    {
        $matcher = new Date('yyyy-MM-dd', $value);
        $generator = $hasGenerator ? new RandomString(10) : null;
        $matcher->setGenerator($generator);
        $formatter = new ValueRequiredFormatter();
        $this->assertSame($result, $formatter->format($matcher));
    }
}
