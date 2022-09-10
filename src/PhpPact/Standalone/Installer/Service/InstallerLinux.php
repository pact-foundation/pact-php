<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Model\Scripts;

/**
 * Class InstallerLinux.
 */
class InstallerLinux extends AbstractInstaller
{
    protected function getFiles(): array
    {
        return [
            [
                'repo'          => 'pact-reference',
                'filename'      => 'pact.h',
                'version'       => self::PACT_FFI_VERSION,
                'versionPrefix' => 'libpact_ffi-v',
                'extract'       => false,
            ],
            [
                'repo'          => 'pact-reference',
                'filename'      => 'libpact_ffi-linux-' . php_uname('m') . '.so.gz',
                'version'       => self::PACT_FFI_VERSION,
                'versionPrefix' => 'libpact_ffi-v',
                'extract'       => true,
                'extractTo'     => 'libpact_ffi.so',
            ],
            [
                'repo'          => 'pact-ruby-standalone',
                'filename'      => 'pact-' . self::PACT_RUBY_STANDALONE_VERSION . '-linux-x86_64.tar.gz',
                'version'       => self::PACT_RUBY_STANDALONE_VERSION,
                'versionPrefix' => 'v',
                'extract'       => true,
            ],
        ];
    }

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
            $binDir . 'pact-stub-service',
            $binDir . 'pact-broker'
        );
    }
}
