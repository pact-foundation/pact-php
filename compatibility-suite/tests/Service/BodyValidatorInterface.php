<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface BodyValidatorInterface
{
    public function validateType(string $path, string $type): void;

    public function validateValue(string $path, string $value): void;
}
