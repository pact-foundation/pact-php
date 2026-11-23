<?php

namespace PhpPact\Standalone\Installer\Model;

/**
 * Represents locations of Ruby Standalone full path and scripts.
 *
 * @internal
 */
class Scripts
{
    /**
     * Destination directory for PACT folder.
     */
    private static string $destinationDir = __DIR__ . '/../../../../..';

    public static function getHeader(): string
    {
        return self::$destinationDir . '/bin/pact-ffi-headers/pact.h';
    }

    public static function getLibrary(): string
    {
        $filename = PHP_OS_FAMILY === 'Windows' ? 'pact_ffi.dll' : (PHP_OS === 'Darwin' ? 'libpact_ffi.dylib' : 'libpact_ffi.so');

        return self::$destinationDir . "/bin/pact-ffi-lib/{$filename}";
    }

    public static function getStubService(): string
    {
        return self::$destinationDir . '/bin/pact-stub-server/pact-stub-server';
    }
}
