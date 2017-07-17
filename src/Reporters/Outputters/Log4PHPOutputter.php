<?php

namespace PhpPact\Reporters\Outputters;

use PHPUnit\Runner\Exception;

class Log4PHPOutputter implements \PhpPact\Reporters\Outputters\IReportOutputter
{
    /**
     * @var \Logger
     */
    private $_logger;

    /**
     * @var string should match the functions available in log4php.  Error level to write the report in.
     */
    private $_writeLevel;

    public function __construct($logger, $writeLevel = 'info')
    {
        $this->_logger = $logger;

        $this->_writeLevel = strtolower($writeLevel);
    }

    public function Write($report)
    {
        $writeLevel = $this->_writeLevel;

        if (!method_exists($this->_logger, $this->_writeLevel)) {
            throw new \Exception(sprintf("Unable to write report at `%s` level as `%s` is not a valid log4php level", $this->_writeLevel, $this->_writeLevel));
        }

        $this->_logger->$writeLevel($report);
    }
}