<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\GeneratorRequiredException;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PHPUnit\Framework\TestCase;

abstract class GeneratorAwareMatcherTestCase extends TestCase
{
    protected GeneratorAwareMatcher $matcher;

    public function testMissingGenerator(): void
    {
        $this->expectException(GeneratorRequiredException::class);
        $this->expectExceptionMessage(sprintf("Generator is required for matcher '%s' when example value is not set", $this->matcher->getType()));
        $this->matcher->setGenerator(null);
        json_encode($this->matcher);
    }
}
