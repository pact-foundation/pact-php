<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\DateTimeFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;

abstract class AbstractDateTimeTestCase extends GeneratorAwareMatcherTestCase
{
    public function testCreateJsonFormatter(): void
    {
        $matcher = $this->getMatcherWithoutExampleValue();
        $this->assertInstanceOf(HasGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = $this->getMatcherWithoutExampleValue();
        $this->assertInstanceOf(DateTimeFormatter::class, $matcher->createExpressionFormatter());
    }
}
