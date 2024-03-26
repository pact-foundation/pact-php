<?php

namespace PhpPact\Standalone\MockService;

use PhpPact\Standalone\Exception\MissingEnvVariableException;

/**
 * An environment variable based mock server configuration.
 */
class MockServerEnvConfig extends MockServerConfig
{
    /**
     * @throws MissingEnvVariableException
     */
    public function __construct()
    {
        if ($host = $this->parseEnv('PACT_MOCK_SERVER_HOST', false)) {
            $this->setHost($host);
        }

        if ($port = $this->parseEnv('PACT_MOCK_SERVER_PORT', false)) {
            $this->setPort((int) $port);
        }

        $this->setConsumer($this->parseEnv('PACT_CONSUMER_NAME'));
        $this->setProvider($this->parseEnv('PACT_PROVIDER_NAME'));
        $this->setPactDir($this->parseEnv('PACT_OUTPUT_DIR', false));

        if ($logDir = $this->parseEnv('PACT_LOG', false)) {
            $this->setLog($logDir);
        }

        if ($logLevel = $this->parseEnv('PACT_LOGLEVEL', false)) {
            $this->setLogLevel($logLevel);
        }

        $version = $this->parseEnv('PACT_SPECIFICATION_VERSION', false);
        if (!$version) {
            $version = static::DEFAULT_SPECIFICATION_VERSION;
        }

        $this->setPactSpecificationVersion($version);
    }

    /**
     * Parse environmental variables to be either null if not required or throw an error if required.
     *
     * @throws MissingEnvVariableException
     */
    private function parseEnv(string $variableName, bool $required = true): ?string
    {
        $result = \getenv($variableName);

        if (is_bool($result)) {
            $result = null;
        }

        if ($required === true && $result === null) {
            throw new MissingEnvVariableException($variableName);
        }

        return $result;
    }
}
