<?php

namespace PhpPact\Reporters\Outputters;

class ConsoleReportOutputter implements ReportOutputterInterface
{
    public function write($report)
    {
        print $report . "\n";
    }
}
