<?php

namespace PhpPactTest\CompatibilitySuite\Service;

final class BodyStorage implements BodyStorageInterface
{
    private string $body;

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
