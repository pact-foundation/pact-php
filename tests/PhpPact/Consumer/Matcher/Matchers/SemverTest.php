<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Semver;

class SemverTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new Semver();
    }

    /**
     * @testWith [null,    "{\"pact:matcher:type\":\"semver\",\"pact:generator:type\":\"Regex\",\"regex\":\"\\\\d+\\\\.\\\\d+\\\\.\\\\d+\"}"]
     *           ["1.2.3", "{\"pact:matcher:type\":\"semver\",\"value\":\"1.2.3\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $this->matcher = new Semver($value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
