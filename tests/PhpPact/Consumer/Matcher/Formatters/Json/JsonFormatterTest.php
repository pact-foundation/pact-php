<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Matchers\MatchingField;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new JsonFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new MatchingField('name');
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage('Matcher does not support json format');
        $this->formatter->format($matcher);
    }

    #[TestWith([new NullValue(), '{"pact:matcher:type":"null"}'])]
    #[TestWith([new Boolean(false), '{"pact:matcher:type":"boolean","value":false}'])]
    public function testFormat(MatcherInterface $matcher, string $json): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertSame($json, json_encode($result));
    }
}
