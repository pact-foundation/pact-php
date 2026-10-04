<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface FixtureLoaderInterface
{
    public function load(string $fileName): string;

    /**
     * @return array<array-key, mixed>
     */
    public function loadJson(string $fileName): array;

    public function isBinary(string $fileName): bool;

    public function determineContentType(string $fileName): string;

    public function getFilePath(string $fileName): string;
}
