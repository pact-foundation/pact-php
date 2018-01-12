<?php

namespace PhpPact\Reporters\Outputters;

class ConsoleReportOutputter implements IReportOutputter
{
    public function write($report)
    {
        echo $report . "\n";
    }
}
