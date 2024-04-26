<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model\Selector;

use PhpPact\Standalone\ProviderVerifier\Exception\InvalidSelectorValueException;
use PhpPact\Standalone\ProviderVerifier\Model\Selector\Selector;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class SelectorTest extends TestCase
{
    #[TestWith(['mainBranch'])]
    #[TestWith(['matchingBranch'])]
    #[TestWith(['deployed'])]
    #[TestWith(['released'])]
    #[TestWith(['deployedOrReleased'])]
    public function testInvalidSelectorValue(string $key): void
    {
        $values = [$key => false];
        $this->expectException(InvalidSelectorValueException::class);
        $this->expectExceptionMessage(sprintf("Value 'false' is not allowed for selector %s", $key));
        new Selector(...$values);
    }

    #[TestWith([new Selector(mainBranch: true), '{"mainBranch":true}'])]
    #[TestWith([new Selector(branch: 'feat-xxx'), '{"branch":"feat-xxx"}'])]
    #[TestWith([new Selector(deployedOrReleased: true), '{"deployedOrReleased":true}'])]
    #[TestWith([new Selector(matchingBranch: true), '{"matchingBranch":true}'])]
    #[TestWith([new Selector(mainBranch: null, branch: 'fix-yyy', fallbackBranch: null, matchingBranch: null, tag: null, fallbackTag: null, deployed: null, released: null, deployedOrReleased: null, environment: null, latest: null, consumer: 'my-consumer'), '{"branch":"fix-yyy","consumer":"my-consumer"}'])]
    public function testJsonSerialize(Selector $selector, string $json): void
    {
        static::assertSame($json, json_encode($selector));
    }
}
