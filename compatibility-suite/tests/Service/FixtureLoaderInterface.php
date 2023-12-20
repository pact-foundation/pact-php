<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface FixtureLoaderInterface
{
    public function load(string $fileName): string;

    public function loadJson(string $fileName): mixed;

    public function isBinary(string $fileName): bool;

    public function determineContentType(string $fileName): string;

    public function getFilePath(string $fileName): string;
}
