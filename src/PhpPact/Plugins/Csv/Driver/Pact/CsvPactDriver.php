<?php

namespace PhpPact\Plugins\Csv\Driver\Pact;

use PhpPact\Plugin\Driver\Pact\AbstractPluginPactDriver;

class CsvPactDriver extends AbstractPluginPactDriver
{
    protected function getPluginName(): string
    {
        return 'csv';
    }
}
