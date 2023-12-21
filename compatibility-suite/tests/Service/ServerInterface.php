<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\VerifyResult;
use Psr\Http\Message\UriInterface;

interface ServerInterface
{
    public function register(int ...$ids): void;

    public function getBaseUri(): UriInterface;

    public function verify(): void;

    public function getVerifyResult(): VerifyResult;

    public function getPactPath(): string;

    public function getPort(): int;
}
