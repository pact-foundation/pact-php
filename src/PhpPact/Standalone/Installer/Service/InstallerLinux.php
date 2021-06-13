<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Model\Scripts;

/**
 * Class InstallerLinux.
 */
class InstallerLinux extends AbstractInstaller
{
    public const FILES = [
        [
            'repo'          => 'pact-ruby-standalone',
            'filename'      => 'pact-' . self::PACT_RUBY_STANDALONE_VERSION . '-linux-x86_64.tar.gz',
            'version'       => self::PACT_RUBY_STANDALONE_VERSION,
            'versionPrefix' => 'v',
            'extract'       => true,
        ],
        [
            'repo'          => 'pact-stub-server',
            'filename'      => 'pact-stub-server-linux-x86_64.gz',
            'version'       => self::PACT_STUB_SERVER_VERSION,
            'versionPrefix' => 'v',
            'extract'       => true,
            'extractTo'     => 'pact-stub-server',
            'executable'    => true,
        ],
        [
            'repo'          => 'pact-reference',
            'filename'      => 'libpact_ffi-linux-x86_64.so.gz',
            'version'       => self::PACT_FFI_VERSION,
            'versionPrefix' => 'libpact_ffi-v',
            'extract'       => true,
            'extractTo'     => 'libpact_ffi.so',
        ],
        ...parent::FILES,
    ];

    /**
     * {@inheritdoc}
     */
    public function isEligible(): bool
    {
        return PHP_OS === 'Linux';
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
            $destinationDir . 'libpact_ffi.so',
            $destinationDir . 'pact-stub-server',
            $binDir . 'pact-broker'
        );
    }
}
