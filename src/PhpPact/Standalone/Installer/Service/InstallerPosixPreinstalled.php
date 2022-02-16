<?php

namespace PhpPact\Standalone\Installer\Service;

use PhpPact\Standalone\Installer\Model\Scripts;

class InstallerPosixPreinstalled implements InstallerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(): bool
    {
        return in_array(PHP_OS, ['Linux', 'Darwin']) && !empty($this->getBinaryPath('pact-provider-verifier'));
    }

    /**
     * {@inheritdoc}
     */
    public function install(string $destinationDir): Scripts
    {
        $scripts = new Scripts(
            'pact-mock-service',
            'pact-stub-service',
            'pact-provider-verifier',
            'pact-message',
            'pact-broker'
        );

        return $scripts;
    }

    private function getBinaryPath(string $binary): string
    {
        return trim((string) shell_exec('command -v ' . escapeshellarg($binary)));
    }
}
