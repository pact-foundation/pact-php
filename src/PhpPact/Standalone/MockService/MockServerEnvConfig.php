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
        if ($host = $this->parseEnv('PACT_MOCK_SERVER_HOST')) {
            $this->setHost($host);
        }

        if ($port = $this->parseEnv('PACT_MOCK_SERVER_PORT')) {
            $this->setPort((int) $port);
        }

        $this->setConsumer($this->parseRequiredEnv('PACT_CONSUMER_NAME'));
        $this->setProvider($this->parseRequiredEnv('PACT_PROVIDER_NAME'));
        $this->setPactDir($this->parseEnv('PACT_OUTPUT_DIR'));

        if ($logDir = $this->parseEnv('PACT_LOG')) {
            $this->setLog($logDir);
        }

        if ($logLevel = $this->parseEnv('PACT_LOGLEVEL')) {
            $this->setLogLevel($logLevel);
        }

        $version = $this->parseEnv('PACT_SPECIFICATION_VERSION');
        if (!$version) {
            /** @var string */
            $version = static::DEFAULT_SPECIFICATION_VERSION;
        }

        $this->setPactSpecificationVersion($version);
    }

    /**
     * Parse environmental variables to be either null if not required or throw an error if required.
     *
     * @throws MissingEnvVariableException
     */
    private function parseEnv(string $variableName): ?string
    {
        $result = \getenv($variableName);

        if (is_bool($result)) {
            $result = null;
        }

        return $result;
    }

    private function parseRequiredEnv(string $variableName): string
    {
        $result = $this->parseEnv($variableName);

        if ($result === null) {
            throw new MissingEnvVariableException($variableName);
        }

        return $result;
    }
}
