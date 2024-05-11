<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\MatchingFieldFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\MatchingField;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MatchingFieldTest extends TestCase
{
    public function testSerialize(): void
    {
        $matcher = new MatchingField('person');
        $this->assertSame(
            "\"matching($'person')\"",
            json_encode($matcher)
        );
    }

    #[TestWith([new NoGeneratorFormatter()])]
    public function testNotSupportedFormatter(FormatterInterface $formatter): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage("MatchingField matcher doesn't support json formatter");
        $matcher = new MatchingField('person');
        $matcher->setFormatter($formatter);
        json_encode($matcher);
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MatchingField('product');
        $this->expectExceptionObject(new MatcherNotSupportedException("MatchingField matcher doesn't support json formatter"));
        $matcher->createJsonFormatter();
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MatchingField('product');
        $this->assertInstanceOf(MatchingFieldFormatter::class, $matcher->createExpressionFormatter());
    }
}
