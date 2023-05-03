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

    /**
     * @return string
     */
    public static function getLibrary(): string
    {
        $extension = PHP_OS_FAMILY === 'Windows' ? 'dll' : (PHP_OS === 'Darwin' ? 'dylib' : 'so');

        return self::$destinationDir . "/bin/pact-ffi-lib/pact.{$extension}";
    }

    public static function getStubService(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-stub-service' . self::getSuffix();
    }

    public static function getProviderVerifier(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-provider-verifier' . self::getSuffix();
    }

    public static function getBroker(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-broker' . self::getSuffix();
    }

    public static function getPactMessage(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-message' . self::getSuffix();
    }

    private static function getSuffix(): string
    {
        return (PHP_OS_FAMILY === 'Windows' ? '.bat' : '');
    }
}
