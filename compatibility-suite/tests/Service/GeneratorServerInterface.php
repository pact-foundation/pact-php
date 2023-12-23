<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface GeneratorServerInterface
{
    public function start(): void;

    public function stop(): void;

    public function getPort(): int;

    public function getBody(): string;

    public function getPath(): string;

    public function getHeader(string $header): array;

    public function getQueryParam(string $name): string;
}
