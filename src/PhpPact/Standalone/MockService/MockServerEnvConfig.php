<?php

namespace PhpPact\Standalone\MockService;

use PhpPact\Standalone\Exception\MissingEnvVariableException;

/**
 * An environment variable based mock server configuration.
 * Class MockServerEnvConfig.
 */
class MockServerEnvConfig extends MockServerConfig
{
    public const DEFAULT_SPECIFICATION_VERSION = '3.0.0';

    /**
     * MockServerEnvConfig constructor.
     *
     * @throws MissingEnvVariableException
     */
    public function __construct()
    {
        parent::__construct();
        $this->setConsumer($this->parseEnv('PACT_CONSUMER_NAME'));
        $this->setPactDir($this->parseEnv('PACT_OUTPUT_DIR'));

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
