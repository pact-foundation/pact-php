<?php

namespace PhpPact\Standalone\Installer\Model;

/**
 * Represents locations of Ruby Standalone full path and scripts.
 * Class Scripts.
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
    public static function getMockService(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-mock-service' . self::getSuffix();
    }

    /**
     * @return string
     */
    public static function getStubService(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-stub-service' . self::getSuffix();
    }

    /**
     * @return string
     */
    public static function getProviderVerifier(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-provider-verifier' . self::getSuffix();
    }

    /**
     * @return string
     */
    public static function getBroker(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-broker' . self::getSuffix();
    }

    /**
     * @return string
     */
    public static function getPactMessage(): string
    {
        return self::$destinationDir . '/bin/pact-ruby-standalone/bin/pact-message' . self::getSuffix();
    }

    private static function getSuffix(): string
    {
        return (PHP_OS_FAMILY === 'Windows' ? '.bat' : '');
    }
}
