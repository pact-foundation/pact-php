<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface BodyStorageInterface
{
    public function setBody(string $body): void;

    public function getBody(): string;
}
