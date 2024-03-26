<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidRegexException;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\Regex;

class RegexTest extends GeneratorAwareMatcherTestCase
{
    private string $regex = '\d+';

    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Regex($this->regex);
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Regex($this->regex, ['1', '23']);
    }

    /**
     * @testWith [null,            "{\"pact:matcher:type\":\"regex\",\"pact:generator:type\":\"Regex\",\"regex\":\"\\\\d+\"}"]
     *           ["number",        null]
     *           [["integer"],     null]
     *           ["12+",           "{\"pact:matcher:type\":\"regex\",\"regex\":\"\\\\d+\",\"value\":\"12+\"}"]
     *           [["12.3", "456"], "{\"pact:matcher:type\":\"regex\",\"regex\":\"\\\\d+\",\"value\":[\"12.3\",\"456\"]}"]
     */
    public function testSerialize(string|array|null $values, ?string $json): void
    {
        if (!$json && $values) {
            $this->expectException(InvalidRegexException::class);
            $value = is_array($values) ? $values[0] : $values;
            $this->expectExceptionMessage("The pattern '{$this->regex}' is not valid for value '{$value}'. Failed with error code 0.");
        }
        $matcher = new Regex($this->regex, $values);
        $this->assertSame($json, json_encode($matcher));
    }
}
