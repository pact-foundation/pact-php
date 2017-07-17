<?php

namespace PhpPact\Reporters;
interface IReporter
{
    public function ReportInfo($infoMessage);

    public function ReportSummary(\PhpPact\Comparers\ComparisonResult $comparisonResult);

    public function ReportFailureReasons(\PhpPact\Comparers\ComparisonResult $comparisonResult);

    public function Indent();

    public function ResetIndentation();

    public function Flush();

}