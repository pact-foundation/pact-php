<?php

namespace PhpPact\Reporters;

interface ReporterInterface
{
    public function reportInfo($infoMessage);

    public function reportSummary(\PhpPact\Comparers\ComparisonResult $comparisonResult);

    public function reportFailureReasons(\PhpPact\Comparers\ComparisonResult $comparisonResult);

    public function indent();

    public function resetIndentation();

    public function flush();
}
