<?php

namespace PhpPactTest\Plugins\Csv\Driver\Pact;

use PhpPact\Plugins\Csv\Driver\Pact\CsvPactDriver;
use PhpPactTest\Plugin\Driver\Pact\AbstractPluginPactDriverTestCase;

class CsvPactDriverTest extends AbstractPluginPactDriverTestCase
{
    protected function createPactDriver(): CsvPactDriver
    {
        return new CsvPactDriver($this->client, $this->config);
    }

    protected function getPluginName(): string
    {
        return 'csv';
    }
}
