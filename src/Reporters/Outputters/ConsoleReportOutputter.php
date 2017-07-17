<?php

namespace PhpPact\Reporters\Outputters;

class ConsoleReportOutputter implements IReportOutputter
{
    public function Write($report)
    {
        echo $report . "\n";
    }
}