<?php

namespace PhpPact\Standalone\MockService;

use PhpPact\Consumer\Exception\MissingEnvVariableException;

/**
 * An environment variable based mock server configuration.
 * Class MockServerEnvConfig
 */
class MockServerEnvConfig extends MockServerConfig
{
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
            ->setPactDir($this->parseEnv('PACT_OUTPUT_DIR', false));
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
        if (\getenv($variableName) !== false) {
            return \getenv($variableName);
        }
        if ($required === true) {
            throw new MissingEnvVariableException($variableName);
        }

        return null;
    }
}
