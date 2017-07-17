<?php

namespace PhpPact;

class PactConfig extends PactBaseConfig
{
    private $_pactDir;

    public function __construct()
    {
        parent::__construct();
        $this->_pactDir = \PhpPact\Constants::DEFAULT_PACT_DIR;
    }

    /**
     * @return string
     */
    public function getPactDir(): string
    {
        return $this->_pactDir;
    }

    /**
     * @param string $pactDir
     * @return PactConfig
     */
    public function setPactDir(string $pactDir)
    {
        $this->_pactDir = $pactDir;
        return $this;
    }

}