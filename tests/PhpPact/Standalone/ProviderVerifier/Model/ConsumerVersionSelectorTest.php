<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelector;
use PHPUnit\Framework\TestCase;

class ConsumerVersionSelectorTest extends TestCase
{
    /**
     * @dataProvider dataProviderForIsValid
     */
    public function testIsValid(
        bool $expected,
        string $pacticipant,
        string $tag,
        string $version,
        bool $latest,
        bool $all
    ) {
        $selector = new ConsumerVersionSelector($pacticipant, $tag, $version, $latest, $all);

        $this->assertEquals($expected, $selector->isValid());
    }

    public function dataProviderForIsValid(): array
    {
        return [
            'no pacticipant' => [true, '', '', '', false, false],
            'all and latest set' => [false, 'foo', '', '', true, true],
            'pacticipant only' => [false, 'foo', '', '', false, false],
            'pacticipant and tag' => [false, 'foo', 'latest', '', false, false],
            'pacticipant, tag and all set' => [false, 'foo', 'latest', '', false, true],
        ];
    }

    public function testJsonSerialize()
    {
        $selector = new ConsumerVersionSelector('', 'foo', '', true, false);
        $expected = [
            'tag' => 'foo',
            'latest' => true,
        ];

        $this->assertEquals($expected, $selector->jsonSerialize());
    }
}
