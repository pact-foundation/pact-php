<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\MinMaxTypeFormatter;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MinMaxTypeFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new MinMaxTypeFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new MinMaxType('example text', 2, 3), '{"pact:matcher:type": "type", "value": ["example text", "example text"], "min": 2, "max": 3}'])]
    #[TestWith([new MinMaxType('example text', -2, 3), '{"pact:matcher:type": "type", "value": ["example text"], "min": 0, "max": 3}'])]
    #[TestWith([new MinMaxType('example text', 2, -3), '{"pact:matcher:type": "type", "value": ["example text", "example text"], "min": 2, "max": 0}'])]
    #[TestWith([new MinMaxType('example text', -2, -3), '{"pact:matcher:type": "type", "value": ["example text"], "min": 0, "max": 0}'])]
    public function testFormat(MinMaxType $matcher, string $json): void
    {
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
