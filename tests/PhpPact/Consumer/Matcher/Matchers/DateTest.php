<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PHPUnit\Framework\Attributes\TestWith;

class DateTest extends AbstractDateTimeTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Date();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Date('yyyy-MM-dd', '2001-09-17');
    }

    #[TestWith([null, '{"pact:matcher:type":"date","pact:generator:type":"Date","format":"yyyy-MM-dd"}'])]
    #[TestWith(['1995-02-04', '{"pact:matcher:type":"date","format":"yyyy-MM-dd","value":"1995-02-04"}'])]
    public function testSerialize(?string $value, string $json): void
    {
        $format = 'yyyy-MM-dd';
        $matcher = new Date($format, $value);
        $this->assertSame($json, json_encode($matcher));
    }
}
