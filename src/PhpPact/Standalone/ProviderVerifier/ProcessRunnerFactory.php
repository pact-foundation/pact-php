<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Standalone\Runner\ProcessRunner;
use Psr\Log\LoggerInterface;

class ProcessRunnerFactory
{
    /**
     * @param string $providerVerifier
     * @param array  $arguments
     *
     * @return ProcessRunner
     */
    public function createRunner(string $providerVerifier, array $arguments, LoggerInterface $logger = null)
    {
        $processRunner = new ProcessRunner($providerVerifier, $arguments);
        if ($logger) {
            $processRunner->setLogger($logger);
        }

        return $processRunner;
    }
}
