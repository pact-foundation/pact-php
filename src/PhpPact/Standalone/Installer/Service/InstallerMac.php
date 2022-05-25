<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Model\Scripts;

/**
 * Class InstallerMac.
 */
class InstallerMac extends AbstractInstaller
{
    public const FILES = [
        [
            'repo'          => 'pact-ruby-standalone',
            'filename'      => 'pact-' . self::PACT_RUBY_STANDALONE_VERSION . '-osx.tar.gz',
            'version'       => self::PACT_RUBY_STANDALONE_VERSION,
            'versionPrefix' => 'v',
            'extract'       => true,
        ],
        [
            'repo'          => 'pact-reference',
            'filename'      => 'libpact_ffi-osx-x86_64.dylib.gz',
            'version'       => self::PACT_FFI_VERSION,
            'versionPrefix' => 'libpact_ffi-v',
            'extract'       => true,
            'extractTo'     => 'libpact_ffi.dylib',
        ],
        ...parent::FILES,
    ];

    /**
     * {@inheritdoc}
     */
    public function isEligible(): bool
    {
        return PHP_OS === 'Darwin';
    }

    /**
     * {@inheritdoc}
     */
    protected function getScripts(string $destinationDir): Scripts
    {
        $destinationDir = $destinationDir . DIRECTORY_SEPARATOR;
        $binDir         = $destinationDir . 'pact' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR;

        return new Scripts(
            $destinationDir . 'pact.h',
            $destinationDir . 'libpact_ffi.dylib',
            $binDir . 'pact-stub-service',
            $binDir . 'pact-broker'
        );
    }
}
