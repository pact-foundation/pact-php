<?php

namespace PhpPact\Standalone\MockService;

use PhpPact\Standalone\Exception\MissingEnvVariableException;

/**
 * An environment variable based mock server configuration.
 * Class MockServerEnvConfig.
 */
class MockServerEnvConfig extends MockServerConfig
{
    const DEFAULT_SPECIFICATION_VERSION = '2.0.0';

    /**
     * MockServerEnvConfig constructor.
     *
     * @throws MissingEnvVariableException
     */
    public function __construct()
    {
        $this
            ->setHost($this->parseEnv('PACT_MOCK_SERVER_HOST'))
            ->setPort($this->parseEnv('PACT_MOCK_SERVER_PORT'))
            ->setConsumer($this->parseEnv('PACT_CONSUMER_NAME'))
            ->setProvider($this->parseEnv('PACT_PROVIDER_NAME'))
            ->setPactDir($this->parseEnv('PACT_OUTPUT_DIR', false))
            ->setCors($this->parseEnv('PACT_CORS', false));

        $timeout = $this->parseEnv('PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT', false);
        if (!$timeout) {
            $timeout = 10;
        }
        $this->setHealthCheckTimeout($timeout);

        $version = $this->parseEnv('PACT_SPECIFICATION_VERSION', false);
        if (!$version) {
            $version = static::DEFAULT_SPECIFICATION_VERSION;
        }

        $this->setPactSpecificationVersion($version);
    }

    /**
     * Parse environmental variables to be either null if not required or throw an error if required.
     *
     * @param string $variableName
     * @param bool   $required
     *
     * @throws MissingEnvVariableException
     *
     * @return null|string
     */
    private function parseEnv(string $variableName, bool $required = true)
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
