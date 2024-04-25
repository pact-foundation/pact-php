<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

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
    #[TestWith(['person', "\"matching($'person')\""])]
    #[TestWith(["probably doesn't work", "\"matching($'probably doesn\\\\'t work')\""])]
    public function testSerialize(string $fieldName, string $json): void
    {
        $matcher = new MatchingField($fieldName);
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            $json,
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
