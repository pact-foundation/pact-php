<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\Selector\Selector;
use PHPUnit\Framework\TestCase;

class ConsumerVersionSelectorsTest extends TestCase
{
    /**
     * @dataProvider selectorsProvider
     */
    public function testJsonSerialize(array $selectors, array $result): void
    {
        static::assertSame($result, iterator_to_array(new ConsumerVersionSelectors($selectors)));
    }

    /**
     * @return array<int, array<mixed>>
     */
    public static function selectorsProvider(): array
    {
        return [
            [['{ "mainBranch": true }', '{ "deployedOrReleased": true }'], ['{ "mainBranch": true }', '{ "deployedOrReleased": true }']],
            [[new Selector(matchingBranch: true), '{ "mainBranch": true }', new Selector(deployedOrReleased: true)], ['{"matchingBranch":true}', '{ "mainBranch": true }', '{"deployedOrReleased":true}']],
            [[new Selector(mainBranch: true)], ['{"mainBranch":true}']],
        ];
    }
}
