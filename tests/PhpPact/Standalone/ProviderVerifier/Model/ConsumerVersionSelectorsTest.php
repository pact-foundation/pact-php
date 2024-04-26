<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\Selector\Selector;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ConsumerVersionSelectorsTest extends TestCase
{
    /**
     * @param string[] $result
     */
    #[TestWith([new ConsumerVersionSelectors(['{ "mainBranch": true }', '{ "deployedOrReleased": true }']), ['{ "mainBranch": true }', '{ "deployedOrReleased": true }']])]
    #[TestWith([new ConsumerVersionSelectors([new Selector(matchingBranch: true), '{ "mainBranch": true }', new Selector(deployedOrReleased: true)]), ['{"matchingBranch":true}', '{ "mainBranch": true }', '{"deployedOrReleased":true}']])]
    #[TestWith([new ConsumerVersionSelectors([new Selector(mainBranch: true)]), ['{"mainBranch":true}']])]
    public function testJsonSerialize(ConsumerVersionSelectors $selectors, array $result): void
    {
        static::assertSame($result, iterator_to_array($selectors));
    }
}
