<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Time;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class HasGeneratorFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new HasGeneratorFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Time('HH:mm'), true, '{"pact:matcher:type": "time", "pact:generator:type": "Regex", "format": "HH:mm", "regex": "\\\\d{2}:\\\\d{2}"}'])]
    #[TestWith([new Time('HH:mm', '12:34'), false, '{"pact:matcher:type": "time", "format": "HH:mm", "value": "12:34"}'])]
    public function testFormat(Time $matcher, bool $hasGenerator, string $json): void
    {
        if ($hasGenerator) {
            $matcher->setGenerator(new Regex('\d{2}:\d{2}'));
        }
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
