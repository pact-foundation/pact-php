<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\StringValueFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StringValueFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new StringValueFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new StringValue(), true, '{"pact:matcher:type": "type", "pact:generator:type": "Regex", "regex": "\\\\w{3}", "value": "some string"}'])]
    #[TestWith([new StringValue('example text'), false, '{"pact:matcher:type": "type", "value": "example text"}'])]
    public function testFormat(StringValue $matcher, bool $hasGenerator, string $json): void
    {
        if ($hasGenerator) {
            $matcher->setGenerator(new Regex('\w{3}'));
        }
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
