<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model\Selector;

use PhpPact\Standalone\ProviderVerifier\Exception\InvalidSelectorValueException;
use PhpPact\Standalone\ProviderVerifier\Model\Selector\Selector;
use PHPUnit\Framework\TestCase;

class SelectorTest extends TestCase
{
    /**
     * @testWith ["mainBranch"]
     *           ["matchingBranch"]
     *           ["deployed"]
     *           ["released"]
     *           ["deployedOrReleased"]
     */
    public function testInvalidSelectorValue(string $key): void
    {
        $values = [$key => false];
        $this->expectException(InvalidSelectorValueException::class);
        $this->expectExceptionMessage(sprintf("Value 'false' is not allowed for selector %s", $key));
        new Selector(...$values);
    }

    /**
     * @testWith [{ "mainBranch": true }, "{\"mainBranch\":true}"]
     *           [{ "branch": "feat-xxx" }, "{\"branch\":\"feat-xxx\"}"]
     *           [{ "deployedOrReleased": true }, "{\"deployedOrReleased\":true}"]
     *           [{ "matchingBranch": true }, "{\"matchingBranch\":true}"]
     *           [{ "mainBranch": null, "branch": "fix-yyy", "fallbackBranch": null, "matchingBranch": null, "tag": null, "fallbackTag": null, "deployed": null, "released": null, "deployedOrReleased": null, "environment": null, "latest": null, "consumer": "my-consumer" }, "{\"branch\":\"fix-yyy\",\"consumer\":\"my-consumer\"}"]
     */
    public function testJsonSerialize(array $values, string $json): void
    {
        static::assertSame($json, json_encode(new Selector(...$values)));
    }
}
