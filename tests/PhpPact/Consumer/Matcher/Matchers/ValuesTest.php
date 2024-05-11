<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\Values;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ValuesTest extends TestCase
{
    /**
     * @param string[] $values
     */
    #[TestWith([['value 1', 'value 2'], '{"pact:matcher:type":"values","value":["value 1","value 2"]}'])]
    #[TestWith([['key 1' => 'value 1', 'key 2' => 'value 2'], '{"pact:matcher:type":"values","value":{"key 1":"value 1","key 2":"value 2"}}'])]
    public function testSerialize(array $values, string $json): void
    {
        $array = new Values($values);
        $this->assertSame($json, json_encode($array));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Values([]);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Values([]);
        $this->expectExceptionObject(new MatcherNotSupportedException("Values matcher doesn't support expression formatter"));
        $matcher->createExpressionFormatter();
    }
}
