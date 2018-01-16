<?php

namespace PhpPact;

use PhpPact\Reporters\Outputters\Log4PHPOutputter;

class PactVerifierConfig extends PactBaseConfig
{
    /**
     * @var array
     */
    private $_reportOutputters;

    public function __construct()
    {
        parent::__construct();

        $this->_reportOutputters   = [];
        $this->_reportOutputters[] = new Log4PHPOutputter($this->_logger);
    }

    /**
     * @return array
     */
    public function getReportOutputters(): array
    {
        return $this->_reportOutputters;
    }

    /**
     * @param array $reportOutputters
     */
    public function setReportOutputters(array $reportOutputters)
    {
        $this->_reportOutputters = $reportOutputters;
    }
}
