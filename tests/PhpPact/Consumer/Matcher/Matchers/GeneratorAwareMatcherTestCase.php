<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\GeneratorNotRequiredException;
use PhpPact\Consumer\Matcher\Exception\GeneratorRequiredException;
use PhpPact\Consumer\Matcher\Generators\Date;
use PhpPact\Consumer\Matcher\Generators\DateTime;
use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Generators\Time;
use PhpPact\Consumer\Matcher\Generators\Uuid;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\TestCase;

abstract class GeneratorAwareMatcherTestCase extends TestCase
{
    public function testGeneratorRequired(): void
    {
        $matcher = $this->getMatcherWithoutExampleValue();
        $this->expectException(GeneratorRequiredException::class);
        $this->expectExceptionMessage(sprintf("Generator is required for matcher '%s' when example value is not set", $matcher->getType()));
        $matcher->setGenerator(null);
        json_encode($matcher);
    }

    /**
     * @dataProvider generatorProvider
     */
    public function testGeneratorNotRequired(GeneratorInterface $generator): void
    {
        $matcher = $this->getMatcherWithExampleValue();
        $this->expectException(GeneratorNotRequiredException::class);
        $this->expectExceptionMessage(sprintf("Generator '%s' is not required for matcher '%s' when example value is set", $generator->getType(), $matcher->getType()));
        $matcher->setGenerator($generator);
        json_encode($matcher);
    }

    /**
     * @return GeneratorInterface[]
     */
    public static function generatorProvider(): array
    {
        return [
            [new Date()],
            [new DateTime()],
            [new MockServerURL('.*(/\d+)$', 'http://example.com/123')],
            [new ProviderState('${key}')],
            [new RandomBoolean()],
            [new RandomDecimal()],
            [new RandomHexadecimal()],
            [new RandomInt()],
            [new RandomString()],
            [new Regex('\w')],
            [new Time()],
            [new Uuid()],
        ];
    }

    abstract protected function getMatcherWithExampleValue(): GeneratorAwareMatcher;

    abstract protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher;
}
