<?php

namespace PhpPact\Provider\Proxy;

use PhpPact\Standalone\Exception\MissingEnvVariableException;


/**
 * An environment variable based mock server configuration.
 * Class MockServerEnvConfig.
 */
class ProxyServerEnvConfig extends ProxyServerConfig
{
    /**
     * ProxyServerConfig constructor.
     *
     * @throws MissingEnvVariableException
     */
    public function __construct()
    {
        $this
            ->setHost($this->parseEnv('PACT_PROXY_SERVER_HOST'))
            ->setPort($this->parseEnv('PACT_PROXY_SERVER_PORT'))
            ->setRootDir($this->parseEnv('PACT_PROXY_ROOT_DIR'))
            ->setPhpExe($this->parseEnv('PACT_PROXY_PHP_EXE', false));
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
