<?php

namespace PhpPact;

class PactBaseConfig
{
    /**
     * @var \Logger
     */
    protected $_logger;

    /**
     * @var bool|string
     */
    protected $_baseUri;

    /**
     * @var bool|string
     */
    protected $_baseUrn;

    /**
     * @var int|string
     */
    protected $_port;

    public function __construct()
    {
        $this->setDefaultLogConfig();
        $this->setBaseUri(\PhpPact\Constants::DEFAULT_HOST, \PhpPact\Constants::DEFAULT_PORT);
    }

    public function setLogConfig($array)
    {
        \Logger::configure($array);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Logger
     */
    public function getLogger($name = 'rootLogger')
    {
        $this->_logger = \Logger::getLogger($name);

        return $this->_logger;
    }

    /**
     * @return mixed
     */
    public function getBaseUri()
    {
        if (!isset($this->_baseUri)) {
            throw new \RuntimeException('Base URL has not been set prior to you getting it');
        }

        return $this->_baseUri;
    }

    /**
     * @param $baseUri -- include http or not
     * @param int    $port     -- customize the port
     * @param string $protocol -- customize the protocol
     *
     * return $this
     */
    public function setBaseUri($baseUri, $port = \PhpPact\Constants::DEFAULT_PORT, $protocol = 'http')
    {
        // handle backslash
        if (\substr($baseUri, -1) == '/') {
            $baseUri = \substr($baseUri, 0, \strlen($baseUri) - 1);
        }

        if (\strtolower(\substr($baseUri, 0, 4)) == 'http') {
            if (\strtolower(\substr($baseUri, 0, 5)) == 'https') {
                // strip off protocol
                // account for ://
                $protocol = 'https';
                $baseUri  = \substr($baseUri, 8, \strlen($baseUri) - 8);
            } else {
                // strip off protocol
                // account for ://
                $protocol = 'http';
                $baseUri  = \substr($baseUri, 7, \strlen($baseUri) - 7);
            }
        }

        $derivedPort = $port;
        // strip off port
        if (\stripos($baseUri, ':')) {
            $ex          = \explode(':', $baseUri);
            $baseUri     = $ex[0];
            $derivedPort = $ex[1];
        }

        $this->_port    = $derivedPort;
        $this->_baseUrn = $baseUri; // sans port and protocol

        // handle http & https
        $baseUri = $protocol . '://' . $baseUri;

        // append port
        if (!\in_array($derivedPort, [80, 443])) {
            $baseUri = $baseUri . ':' . $derivedPort;
        }

        $this->_baseUri = $baseUri;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getPort()
    {
        if (!isset($this->_port)) {
            throw new \RuntimeException('Port not been set prior to you getting it');
        }

        return $this->_port;
    }

    /**
     * URL sans port and protocol
     *
     * @return bool|string
     */
    public function getBaseUrn()
    {
        if (!isset($this->_baseUrn)) {
            throw new \RuntimeException('Port not been set prior to you getting it');
        }

        return $this->_baseUrn;
    }

    /**
     * Provide a base log4php config
     */
    protected function setDefaultLogConfig()
    {
        $defaultLogValue = \ini_get('error_log');

        // if there is no log set, send everything to stderr
        $consoleLevel = 'info';
        if (!$defaultLogValue) {
            $consoleLevel = 'DEBUG';
        }

        $appenders = [
            'console' => [
                'class'  => 'LoggerAppenderConsole',
                'layout' => [
                    'class' => 'LoggerLayoutSimple'
                ],
                'threshold' => $consoleLevel
            ]];

        // if there is log set, add another appender
        if ($defaultLogValue != false && $defaultLogValue != 'stderr') {
            if ($defaultLogValue != 'syslog') {
                $appenders['file'] = [
                    'class'  => 'LoggerAppenderFile',
                    'layout' => [
                        'class' => 'LoggerLayoutSimple'
                    ],
                    'params' => [
                        'file' => $defaultLogValue],
                    'append' => true
                ];
            } else {
                $appenders['syslog'] = [
                    'class'  => 'LoggerAppenderSyslog',
                    'layout' => [
                        'class' => 'LoggerLayoutSimple'
                    ]
                ];
            }
        }

        \Logger::configure([
            'rootLogger' => [
                'appenders' => \array_keys($appenders),
                'level'     => 'DEBUG'
            ],
            'appenders' => $appenders
        ]);

        $this->_logger = \Logger::getLogger('rootLogger');

        return $this;
    }
}
