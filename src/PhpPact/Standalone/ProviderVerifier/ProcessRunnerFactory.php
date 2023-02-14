<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Runner\ProcessRunner;
use Psr\Log\LoggerInterface;

class ProcessRunnerFactory
{
    private string $providerVerifier;

    public function __construct(?string $providerVerifier = null)
    {
        $this->providerVerifier = $providerVerifier ?: Scripts::getProviderVerifier();
    }

    /**
     * @param array<int, string> $arguments
     */
    public function createRunner(array $arguments, LoggerInterface $logger = null): ProcessRunner
    {
        $processRunner = new ProcessRunner($this->providerVerifier, $arguments);
        if ($logger) {
            $processRunner->setLogger($logger);
        }

        return $processRunner;
    }
}
