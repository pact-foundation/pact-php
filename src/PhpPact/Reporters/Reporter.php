<?php

namespace PhpPact\Reporters;

class Reporter implements IReporter
{
    private $_outputters;

    private $_currentTabDepth;
    private $_failureInfoCount;
    private $_failureCount;

    private $_reportLines = [];

    public function __construct($config, $outputters = [])
    {
        if (\count($outputters) > 0) {
            $this->_outputters = $outputters;
        } elseif ($config instanceof \PhpPact\PactVerifierConfig) {
            $this->_outputters = $config->getReportOutputters();
        } else {
            throw new \Exception('Invalid parameters. Either provide a valid config or valid outputters');
        }
    }

    public function reportInfo($infoMessage)
    {
        $this->addReportLine($infoMessage, $this->_currentTabDepth);
    }

    public function reportSummary(\PhpPact\Comparers\ComparisonResult $comparisonResult)
    {
        $this->addSummary($comparisonResult);
    }

    public function reportFailureReasons(\PhpPact\Comparers\ComparisonResult $comparisonResult)
    {
        $this->writeFailureReasons($comparisonResult);
    }

    public function indent()
    {
        $this->_currentTabDepth++;
    }

    public function resetIndentation()
    {
        $this->_currentTabDepth = 0;
    }

    public function flush()
    {
        if (empty($this->_reportLines) || $this->_outputters == null) {
            return;
        }

        foreach ($this->_outputters as $outputter) {
            /*
             * @var \PhpPact\Reporters\Outputters\IReportOutputter $outputter
             */
            $outputter->write(\implode(PHP_EOL, $this->_reportLines));
        }
    }

    private function addSummary(\PhpPact\Comparers\ComparisonResult $comparisonResult, $tabDepth = 0)
    {
        if ($comparisonResult == null) {
            return;
        }

        if ($comparisonResult->hasFailure()) {
            $failureBuilder = '';

            $shallowFailureCount = $comparisonResult->shallowFailureCount();

            if ($shallowFailureCount > 0) {
                $failureBuilder .= ' (FAILED - ';
                for ($i = 0; $i < $shallowFailureCount; $i++) {
                    $this->_failureInfoCount++;

                    $failureBuilder .= "{$this->_failureInfoCount}";

                    if ($i < $shallowFailureCount - 1) {
                        $failureBuilder .= ', ';
                    }
                }

                $failureBuilder .= ')';
            }

            $this->addReportLine($comparisonResult->getMessage() . $failureBuilder, $this->_currentTabDepth + $tabDepth);
        } else {
            $this->addReportLine($comparisonResult->getMessage(), $this->_currentTabDepth + $tabDepth);
        }

        foreach ($comparisonResult->childResults() as $childComparisonResult) {
            $this->addSummary($childComparisonResult, $tabDepth + 1);
        }
    }

    private function writeFailureReasons(\PhpPact\Comparers\ComparisonResult $comparisonResult)
    {
        if ($comparisonResult == null) {
            return;
        }

        if (!$comparisonResult->hasFailure()) {
            return;
        }

        $this->addReportLine('', 0);
        $this->addReportLine('Failures:', 0);

        foreach ($comparisonResult->failures() as $failure) {
            $this->addReportLine('', 0);
            $this->addReportLine(\sprintf('%s %s', $this->_failureCount, $failure->getResult()), 0);
        }
    }

    private function addReportLine($message, $tabDepth)
    {
        $a                    = \array_fill(0, $tabDepth * 2, ' '); //Each tab we want to be 2 space chars
        $indentation          = \implode('', $a);
        $this->_reportLines[] = $indentation . $message;
    }
}
