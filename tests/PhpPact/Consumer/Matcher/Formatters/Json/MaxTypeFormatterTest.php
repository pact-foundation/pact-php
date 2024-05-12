<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\MaxTypeFormatter;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MaxTypeFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new MaxTypeFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new MaxType('example text', 2, true), '{"pact:matcher:type": "type", "value": ["example text"], "max": 2}'])]
    #[TestWith([new MaxType('example text', -2, true), '{"pact:matcher:type": "type", "value": ["example text"], "max": 0}'])]
    #[TestWith([new MaxType('example text', 2, false), '{"pact:matcher:type": "type", "value": ["example text"], "max": 2}'])]
    #[TestWith([new MaxType('example text', -2, false), '{"pact:matcher:type": "type", "value": ["example text"], "max": 0}'])]
    public function testFormat(MaxType $matcher, string $json): void
    {
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
