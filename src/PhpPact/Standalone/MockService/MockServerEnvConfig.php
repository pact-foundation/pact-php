<?php

namespace PhpPact\Standalone\MockService;

use PhpPact\Standalone\Exception\MissingEnvVariableException;

/**
 * An environment variable based mock server configuration.
 */
class MockServerEnvConfig extends MockServerConfig
{
    public const DEFAULT_SPECIFICATION_VERSION = '2.0.0';

    /**
     * @throws MissingEnvVariableException
     */
    public function __construct()
    {
        $this->setHost($this->parseEnv('PACT_MOCK_SERVER_HOST'));
        $this->setPort((int) $this->parseEnv('PACT_MOCK_SERVER_PORT'));
        $this->setConsumer($this->parseEnv('PACT_CONSUMER_NAME'));
        $this->setProvider($this->parseEnv('PACT_PROVIDER_NAME'));
        $this->setPactDir($this->parseEnv('PACT_OUTPUT_DIR', false));
        $this->setCors($this->parseEnv('PACT_CORS', false));

        if ($logDir = $this->parseEnv('PACT_LOG', false)) {
            $this->setLog($logDir);
        }

        if ($logLevel = $this->parseEnv('PACT_LOGLEVEL', false)) {
            $this->setLogLevel($logLevel);
        }

        $timeout = $this->parseEnv('PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT', false);
        if (!$timeout) {
            $timeout = 10;
        }
        $this->setHealthCheckTimeout($timeout);

        $seconds = $this->parseEnv('PACT_MOCK_SERVER_HEALTH_CHECK_RETRY_SEC', false);
        if (!$seconds) {
            $seconds = 1;
        }
        $this->setHealthCheckRetrySec($seconds);

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
    private function parseEnv(string $variableName, bool $required = true): mixed
    {
        $result = null;

        if (\getenv($variableName) === 'false') {
            $result = false;
        } elseif (\getenv($variableName) === 'true') {
            $result = true;
        }
        if (\getenv($variableName) !== false) {
            $result = \getenv($variableName);
        }

        if ($required === true && $result === null) {
            throw new MissingEnvVariableException($variableName);
        }

        return $result;
    }
}
