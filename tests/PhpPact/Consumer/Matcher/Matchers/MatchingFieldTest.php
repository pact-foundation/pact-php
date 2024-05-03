<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\MinimalFormatter;
use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;
use PhpPact\Consumer\Matcher\Formatters\ValueOptionalFormatter;
use PhpPact\Consumer\Matcher\Formatters\ValueRequiredFormatter;
use PhpPact\Consumer\Matcher\Formatters\XmlContentFormatter;
use PhpPact\Consumer\Matcher\Formatters\XmlElementFormatter;
use PhpPact\Consumer\Matcher\Matchers\MatchingField;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MatchingFieldTest extends TestCase
{
    public function testInvalidField(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('String value "probably doesn\'t work" should not contains single quote');
        $matcher = new MatchingField("probably doesn't work");
        $matcher->setFormatter(new PluginFormatter());
        json_encode($matcher);
    }

    public function testSerialize(): void
    {
        $matcher = new MatchingField('person');
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            "\"matching($'person')\"",
            json_encode($matcher)
        );
    }

    #[TestWith([new MinimalFormatter()])]
    #[TestWith([new ValueOptionalFormatter()])]
    #[TestWith([new ValueRequiredFormatter()])]
    #[TestWith([new XmlContentFormatter()])]
    #[TestWith([new XmlElementFormatter()])]
    public function testNotSupportedFormatter(FormatterInterface $formatter): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage('MatchingField matcher only work with plugin');
        $matcher = new MatchingField('person');
        $matcher->setFormatter($formatter);
        json_encode($matcher);
    }
}
