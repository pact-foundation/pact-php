<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Formatters\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PHPUnit\Framework\TestCase;

class XmlContentFormatterTest extends TestCase
{
    /**
     * @testWith [true,  "2001-01-02", {"content": "2001-01-02", "matcher": {"pact:matcher:type": "date", "format": "yyyy-MM-dd", "size": 10}, "pact:generator:type": "RandomString"}]
     *           [false, "2002-02-03", {"content": "2002-02-03", "matcher": {"pact:matcher:type": "date", "format": "yyyy-MM-dd"}}]
     */
    public function testFormat(bool $hasGenerator, ?string $value, array $result): void
    {
        $matcher = new Date('yyyy-MM-dd', 5);
        $generator = $hasGenerator ? new RandomString(10) : null;
        $formatter = new XmlContentFormatter();
        $this->assertSame($result, $formatter->format($matcher, $generator, $value));
    }
}
