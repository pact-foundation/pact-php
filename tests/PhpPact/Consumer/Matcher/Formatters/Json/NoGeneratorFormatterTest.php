<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NoGeneratorFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NoGeneratorFormatter();
    }

    #[TestWith([new EachKey(['key' => 'value'], [new Includes('value')]), <<<JSON
        {
            "pact:matcher:type": "eachKey",
            "rules": [
                {
                    "pact:matcher:type": "include",
                    "value": "value"
                }
            ],
            "value": {
                "key": "value"
            }
        }
        JSON
    ])]
    public function testFormat(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
