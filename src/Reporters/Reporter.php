<?php

namespace PhpPact\Reporters;

use PHPUnit\Runner\Exception;

class Reporter implements IReporter
{
    private $_outputters;

    private $_currentTabDepth;
    private $_failureInfoCount;
    private $_failureCount;

    private $_reportLines = array();

    public function __construct($config, $outputters = array())
    {
        if (count($outputters) > 0) {
            $this->_outputters = $outputters;
        } elseif ($config instanceof \PhpPact\PactVerifierConfig) {
            $this->_outputters = $config->getReportOutputters();
        } else {
            throw new Exception("Invalid parameters. Either provide a valid config or valid outputters");
        }
    }


    public function ReportInfo($infoMessage)
    {
        $this->AddReportLine($infoMessage, $this->_currentTabDepth);
    }

    public function ReportSummary(\PhpPact\Comparers\ComparisonResult $comparisonResult)
    {
        $this->AddSummary($comparisonResult);
    }

    public function ReportFailureReasons(\PhpPact\Comparers\ComparisonResult $comparisonResult)
    {
        $this->WriteFailureReasons($comparisonResult);
    }

    public function Indent()
    {
        $this->_currentTabDepth++;
    }

    public function ResetIndentation()
    {
        $this->_currentTabDepth = 0;
    }

    public function Flush()
    {
        if (empty($this->_reportLines) || $this->_outputters == null) {
            return;
        }

        foreach ($this->_outputters as $outputter) {
            /**
             * @var \PhpPact\Reporters\Outputters\IReportOutputter $outputter
             */
            $outputter->Write(implode(PHP_EOL, $this->_reportLines));
        }
    }

    private function AddSummary(\PhpPact\Comparers\ComparisonResult $comparisonResult, $tabDepth = 0)
    {
        if ($comparisonResult == null) {
            return;
        }

        if ($comparisonResult->hasFailure()) {
            $failureBuilder = '';

            $shallowFailureCount = $comparisonResult->shallowFailureCount();

            if ($shallowFailureCount > 0) {
                $failureBuilder .= " (FAILED - ";
                for ($i = 0; $i < $shallowFailureCount; $i++) {
                    $this->_failureInfoCount++;

                    $failureBuilder .= "{$this->_failureInfoCount}";

                    if ($i < $shallowFailureCount - 1) {
                        $failureBuilder .= ", ";
                    }
                }

                $failureBuilder .= ")";
            }

            $this->AddReportLine($comparisonResult->getMessage() . $failureBuilder, $this->_currentTabDepth + $tabDepth);
        } else {
            $this->AddReportLine($comparisonResult->getMessage(), $this->_currentTabDepth + $tabDepth);
        }

        foreach ($comparisonResult->childResults() as $childComparisonResult) {
            $this->AddSummary($childComparisonResult, $tabDepth + 1);
        }
    }

    private function WriteFailureReasons(\PhpPact\Comparers\ComparisonResult $comparisonResult)
    {
        if ($comparisonResult == null) {
            return;
        }

        if (!$comparisonResult->hasFailure()) {
            return;
        }

        $this->AddReportLine('', 0);
        $this->AddReportLine("Failures:", 0);

        foreach ($comparisonResult->failures() as $failure) {
            $this->AddReportLine('', 0);
            $this->AddReportLine(sprintf("%s %s", $this->_failureCount, $failure->getResult()), 0);
        }
    }

    private function AddReportLine($message, $tabDepth)
    {
        $a = array_fill(0, $tabDepth * 2, ' '); //Each tab we want to be 2 space chars
        $indentation = implode('', $a);
        $this->_reportLines[] = $indentation . $message;
    }
}
