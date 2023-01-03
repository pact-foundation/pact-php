<?php

namespace PhpPact\Standalone\Installer\Model;

/**
 * Represents locations of Ruby Standalone full path and scripts.
 * Class Scripts.
 *
 * @internal
 */
class Scripts
{
    /**
     * Destination directory for PACT folder.
     *
     * @var string
     */
    private static string $destinationDir = __DIR__ . '/../../../../..';

    /**
     * @return string
     */
    public static function getHeader(): string
    {
        return self::$destinationDir . '/bin/pact-ffi-headers/pact.h';
    }

    /**
     * @return string
     */
    public static function getLibrary(): string
    {
        $extension = PHP_OS_FAMILY === 'Windows' ? 'dll' : (PHP_OS === 'Darwin' ? 'dylib' : 'so');

        return self::$destinationDir . "/bin/pact-ffi-lib/pact.{$extension}";
    }

    /**
     * @return string
     */
    public static function getStubService(): string
    {
        return self::$destinationDir . '/bin/pact-stub-server/pact-stub-server';
    }

    /**
     * @return string
     */
    public static function getBroker(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-broker';
    }
}
