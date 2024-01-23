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
use PHPUnit\Framework\TestCase;

class MatchingFieldTest extends TestCase
{
    /**
     * @testWith ["person",                "\"matching($'person')\""]
     *           ["probably doesn't work", "\"matching($'probably doesn\\\\'t work')\""]
     */
    public function testSerialize(string $fieldName, string $json): void
    {
        $matcher = new MatchingField($fieldName);
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            $json,
            json_encode($matcher)
        );
    }

    /**
     * @dataProvider formatterProvider
     */
    public function testNotSupportedFormatter(string $formatterClassName): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage('MatchingField matcher only work with plugin');
        $matcher = new MatchingField('person');
        $matcher->setFormatter(new $formatterClassName());
        json_encode($matcher);
    }

    public function formatterProvider(): array
    {
        return [
            [MinimalFormatter::class],
            [ValueOptionalFormatter::class],
            [ValueRequiredFormatter::class],
            [XmlContentFormatter::class],
            [XmlElementFormatter::class],
        ];
    }
}
