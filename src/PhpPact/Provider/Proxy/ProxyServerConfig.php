<?php

namespace PhpPact\Provider\Proxy;

class ProxyServerConfig
{
    /**
     * Host on which to bind the service.
     *
     * @var string
     */
    private $host = 'localhost';

    /**
     * Port on which to run the service.
     *
     * @var int
     */
    private $port = 7201;

    /**
     * Root dir for the server to run
     *
     * @var string
     */
    private $rootDir = 'public';

    /**
     * Override the php.exe location
     *
     * @var string
     */
    private $phpExe = 'php';

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return ProxyServerConfig
     */
    public function setHost(string $host): ProxyServerConfig
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return ProxyServerConfig
     */
    public function setPort(int $port): ProxyServerConfig
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhpExe(): string
    {
        return $this->phpExe;
    }

    /**
     * @param string $phpExe
     * @return ProxyServerConfig
     */
    public function setPhpExe(string $phpExe): ProxyServerConfig
    {
        $this->phpExe = $phpExe;
        return $this;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    /**
     * @param string $rootDir
     * @return ProxyServerConfig
     */
    public function setRootDir(string $rootDir): ProxyServerConfig
    {
        $this->rootDir = $rootDir;
        return $this;
    }

}