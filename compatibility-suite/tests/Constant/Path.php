<?php

namespace PhpPactTest\CompatibilitySuite\Constant;

class Path
{
    public const ROOT_PATH = __DIR__ . '/../..';
    public const PACTS_PATH = self::ROOT_PATH . '/pacts';
    public const FIXTURES_PATH = self::ROOT_PATH . '/pact-compatibility-suite/fixtures';
    public const PUBLIC_PATH = self::ROOT_PATH . '/public';
}
